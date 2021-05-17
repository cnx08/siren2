
require('events').EventEmitter.defaultMaxListeners = Infinity;
var express = require('express');
var path = require('path');
//var http = require('http');
var config = require('./config/index.js');
var log = require('./libs/log')(module);
var cookieParser = require('cookie-parser');
var bodyParser = require('body-parser');
var logger = require('morgan');
var routes = require('./routes/index');
var net = require('net');

var pg = require('pg');
var config_pg = {
    user: 'postgres', //env var: PGUSER
    database: 'testutf', //env var: PGDATABASE
    password: 'deparol', //env var: PGPASSWORD
    host: 'localhost', // Server hosting the postgres database
    port: 5432, //env var: PGPORT
    max: 10, // max number of clients in the pool
    idleTimeoutMillis: 30000, // how long a client is allowed to remain idle before being closed
};

var pool = new pg.Pool(config_pg);

var app = express();

var HOST = '127.0.0.1';
var PORT = 20000;//listen from exchange_commands.php
// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');
app.set('port',config.get('port'));

//app.use(favicon(path.join(__dirname, 'public', 'favicon.ico')));
app.use(logger('dev'));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: false }));
app.use(cookieParser());

app.use('/',routes);

app.use(express.static(path.join(__dirname, 'public')));


var server = app.listen(config.get('port'));

function addZero(n) {//для отображения времени
    return n.length > 1 ? n : "0" + n;
}
function addRubish(str) {//"экранируем" user-agent
    var hua_len = str.length * 2;
    var ih = 0;
    while (ih < hua_len) {
        str = str.substr(0, ih) + str.substr(ih, 1) + '?' + str.substr(ih + 1);
        ih = ih + 2;
    }
    return str;
}
function cleanIp(str) {//ipv6 to ipv4
    var ip4 = str.substr(str.lastIndexOf(':') + 1);
    if (ip4 == '1') ip4 = '127.0.0.1';
    return ip4;
}
function cookieToObj(str) {//преобразуем строку куки чтобы её разобрать как json
    var cooka = '';
    if (typeof str != 'undefined') {
        cooka = JSON.parse("{\"" + str.replace(/=/g, "\":\"").replace(/; /g, "\",\"") + "\"}");
    }
    return cooka;
}
var io = require('socket.io').listen(server);
var Users = new Array();
io.on('connection', function(socket) {
    var ip = cleanIp(socket.handshake.address);
    var hua = addRubish(socket.handshake.headers['user-agent']);
    var cooka = cookieToObj(socket.handshake.headers.cookie);

    log.info('a user connected ' + ip);
    var check_flag = 1;
    if (typeof cooka.code_user != 'undefined' && typeof cooka.id_user != 'undefined') {
        if (cooka.code_user.match(/[^a-zA-Z0-9]/)) {
            check_flag = 0;
        }
    }
    if (check_flag === 1 && typeof hua != 'undefined') {
        pool.connect(function(err, client, done) {//Лезем в базу за данными по authorization
            if(err) {
                return log.error('error fetching client from pool', err);
            }
            client.query('SELECT * from pr_photo_auth($1::int, $2::text, $3::text, $4::text)', [cooka.id_user, cooka.code_user, hua, ip], function(err, result) {

                if(err) {
                    done();//call `done()` to release the client back to the pool
                    return log.error('error running query pr_photo_auth', err);
                }
                if (result.rowCount == 0){
                    done();//call `done()` to release the client back to the pool
                    return log.info('Не правильно сработала pr_photo_auth');
                }
                else {
                    var auth = result.rows[0].value;
                    if(auth == 1){
                        log.info('user with ip ' + ip+ ' logined');
                        //объект с юзером, который полетит в общий массив
                        var user = {
                                        db_id_user : cooka.id_user,
                                        connection_id : socket.id
                                    };
                        //лезем за турникетами
                        client.query('SELECT num,name from base_turn_s', [], function(err2, result2) {
                            if(err2) {
                                done();//call `done()` to release the client back to the pool
                                return log.error('error running query base_turn_s', err2);
                            }
                            if (result2.rowCount == 0){
                                done();
                                return log.info('rowCount == 0 base_turn_s');
                            }
                            else {
                                var turnlist = {0:"Не показывать"};
                                for(i= 0; i<result2.rowCount; i++){
                                    turnlist[result2.rows[i].num] = result2.rows[i].name;
                                }
                                io.to(socket.id).emit('turnlist',turnlist);

                            }
                        });
                        //лезем за опциями окна
                        client.query('SELECT * from photo_config where user_id = $1::int', [cooka.id_user], function(err1, result1) {
                            if(err1) {
                                done();
                                return log.error('error running query photo_config', err1);
                            }
                            if (result1.rowCount == 0){
                                done();
                                return log.info('Не правильно сработала pr_photo_auth');
                            }
                            else {
                                user.window_1 = result1.rows[0].tur_first_win;
                                user.window_2 = result1.rows[0].tur_second_win;
                                user.window_3 = result1.rows[0].tur_third_win;
                                user.window_4 = result1.rows[0].tur_fours_win;
                                user.ch_show_third_fours  = result1.rows[0].ch_show_third_fours;

                                if (user.ch_show_third_fours == '1' && (user.window_3 > 0 || user.window_4 > 0)){
                                    io.to(socket.id).emit('openSecondWindow');
                                    setTimeout(function(){io.to(socket.id).emit('params2',result1.rows[0])},500);
                                }

                                io.to(socket.id).emit('params',result1.rows[0]);
                                done();
                            }
                        });

                        Users.push(user);
                    }
                    else{//redirect to login form
                        done();
                        io.to(socket.id).emit('redirect');
                        log.info('user with ip ' + ip+ 'redirected to login form - bad data');
                    }
                }
            });
        });

        pool.on('error', function (err, client) {
            log.error('idle client error', err.message, err.stack)
        })


    }
    else{//redirect to login form
        io.to(socket.id).emit('redirect');
        log.info('user with ip ' + ip+ 'redirected to login form - undefined data');
    }
    socket.on('update', function(data){ //edit photo_config
        var qStr = 'update photo_config set ';
        var i=1;
        var valArr = [];
        var len = Object.keys(data).length;
        var zap = ', ';
        for (var prop in data){
            log.info(prop + ' set to  ' + data[prop]);
            var format = prop.indexOf('tur_')===0 ? 'int' : 'text';
            var val = data[prop];
            if( val===false ) val = '0';
            if( val===true ) val = '1';
            valArr.push(val);
            if (i==len) zap = ' ';
            qStr += prop +'=$'+i+'::'+format+zap
            i++;
        }
        valArr.push(cooka.id_user);
        qStr +=' where user_id = $'+i+'::int';
        //log.info(qStr);
        pool.connect(function(err, client, done) {
            if(err) {
                return log.error('error fetching client from pool', err);
            }
            client.query('insert into temp_user_id_ip_info values ($1::int,$2::text,$3::text)', [cooka.id_user,ip,hua], function(err, result) {//update temp_user_id_ip_info
                if(err) {
                    return log.error('error insert into temp_user_id_ip_info', err);
                }
                if (result.rowCount == 0){
                    return log.info('rowCount == 0 insert into temp_user_id_ip_info');
                }
                else {
                    return log.info('rowCount != 0 insert into temp_user_id_ip_info');
                }
            });
            client.query(qStr, valArr, function(err2, result2) {//update photo_config
                done();
                if(err2) {
                    return log.error('error update photo_config', err2);
                }
                if (result2.rowCount == 0){
                    return log.info('rowCount == 0 update photo_config');
                }
                else {
                    return log.info('rowCount != 0 updated photo_config');
                }
            });
        });

    });
    socket.on('disconnect', function(){
        log.info('user disconnected ' + ip);

    });
});

var socketServer = net.createServer(function(sock) {
    sock.on('data', function(data) {
        var data_listeners = new Array();
        //console.log('DATA ' + sock.remoteAddress + ': ' + data);
        log.info('DATA: ' + data);
        var arr_event = String(data).split(';');
        var turn = arr_event[2];
        Users.forEach(function(item, i, Users) {
            if(item.window_1 == turn || item.window_2 == turn || ((item.window_1 == turn || item.window_2 == turn) && item.ch_show_third_fours == '1')){
                data_listeners.push(item.connection_id);
            }
        });
        if (data_listeners.length > 0) {//если есть кому отправлять
            var descr = config.get(arr_event[1]);
            var time_str = addZero(arr_event[3]) + ':' + addZero(arr_event[4]) + ':' + addZero(arr_event[5]);
            var small_frame = arr_event[1] === 'k' || arr_event[1] === 'K' ? 0 : 1;


            //Лезем в базу за данными по px_code
            pool.connect(function (err, client, done) {
                if (err) {
                    return log.error('error fetching client from pool', err);
                }
                client.query('SELECT * from t_photo where code = $1::text', [arr_event[6]], function (err, result) {
                    //call `done()` to release the client back to the pool
                    done();

                    if (err) {
                        return log.error('error running query t_photo', err);
                    }
                    if (result.rowCount == 0) {
                        var to_send = {
                            "turn": turn,
                            "code_descr": descr,
                            "red_code": "1",
                            "time_str": time_str,
                            "FIO": "",
                            "dept": "",
                            "position": "",
                            "smena_name": "",
                            "sm_time": "",
                            "sm_dinner": "",
                            "sm_frame": small_frame,
                            "photo_name": ""
                        };
                        data_listeners.forEach(function(item, i, data_listeners) {
                            io.to(item).emit('event', to_send);
                        });
                        return log.info('Нет такого пропуска в t_photo');
                    }
                    else {
                        var to_send = {
                            "turn": turn,
                            "code_descr": descr,
                            "red_code": "0",
                            "time_str": time_str,
                            "FIO": result.rows[0].fio,
                            "dept": result.rows[0].dname,
                            "position": result.rows[0].position,
                            "smena_name": result.rows[0].sname,
                            "sm_time": result.rows[0].smena,
                            "sm_dinner": result.rows[0].obed,
                            "sm_frame": small_frame,
                            "photo_name": result.rows[0].photo
                        };

                        data_listeners.forEach(function(item, i, data_listeners) {
                            io.to(item).emit('event', to_send);
                        });
                    }
                });
            });

            pool.on('error', function (err, client) {
                log.error('idle client error', err.message, err.stack)
            })
        }



    });
});

socketServer.on('error', function(err) {
    // handle errors here
    throw err;
});

socketServer.listen(PORT, HOST);