const express = require('express');
const ftp = require('ftp');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

const app = express();
  
app.get('/download', (req, res, next) => {
    try {
        const c = new ftp();
    
        c.on('ready', function() {
            res.setHeader('Content-Type', req.query.mimetype);
            res.setHeader('Content-disposition', 'attachment; filename=' + path.basename(req.query.file));
            c.get(req.query.file, function(err, stream) {
                console.log("ok")
                if (err) throw err;
                stream.once('close', function() { c.end(); });
                stream.pipe(res);
            });
        });

        //connect to the ftp server
        c.connect({ host: req.query.host, user: req.query.username, password: req.query.password });
    } catch (error) {
        // throw(error)
    }
    
 
});

app.get('', (req, res, next) => {
    throw "ok";
    res.send("ko")
})

// process.on('uncaughtException', function (err) {
//     console.error((new Date).toUTCString() + ' uncaughtException:', err.message)
//     console.error(err.stack)
// });

// process.on('unhandledRejection', (reason, p) => {
//     console.error((new Date).toUTCString() + ' Unhandled Rejection at: Promise', p, 'reason:', reason);
// });

app.use(function(err, req, res, next) {
    console.error(err);
    res.status(500).send('Something broke!');
});


const host = 'localhost';
app.listen(3000, host, () => {
    console.log('Server listening on ' +  host + ' port 3000!');
})

