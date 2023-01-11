const express = require('express');
const ftp = require('ftp');
const fs = require('fs');
const path = require('path');
require('dotenv').config();

const app = express();

app.get('/download', (req, res) => {

  const c = new ftp();

  c.on('ready', function() {
    res.setHeader('Content-Type', req.query.mimetype);
    res.setHeader('Content-disposition', 'attachment; filename=' + path.basename(req.query.file));
    c.get(req.query.file, function(err, stream) {
      if (err) throw err;
      stream.once('close', function() { c.end(); });
      stream.pipe(res);
    });
  });
  //connect to the ftp server
  c.connect({ host: req.query.host, user: req.query.username, password: req.query.password });
});

const host = 'localhost';
app.listen(3000, host, () => {
    console.log('Server listening on ' +  host + ' port 3000!');
})

