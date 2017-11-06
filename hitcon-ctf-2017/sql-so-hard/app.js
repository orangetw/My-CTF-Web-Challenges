#!/usr/bin/node

/**
 *  @HITCON CTF 2017
 *  @Author Orange Tsai
 */

const qs = require("qs");
const fs = require("fs");
const pg = require("pg");
const mysql = require("mysql");
const crypto = require("crypto");
const express = require("express");

const pool = mysql.createPool({
    connectionLimit: 100, 
    host: "localhost",
    user: "ban",
    password: "ban",
    database: "bandb",
});

const client = new pg.Client({
    host: "localhost",
    user: "userdb",
    password: "userdb",
    database: "userdb",
});
client.connect();

const KEYWORDS = [
    "select", 
    "union", 
    "and", 
    "or", 
    "\\", 
    "/", 
    "*", 
    " " 
]

function waf(string) {
    for (var i in KEYWORDS) {
        var key = KEYWORDS[i];
        if (string.toLowerCase().indexOf(key) !== -1) {
            return true;
        }
    }
    return false;
}

const app = express();
app.use((req, res, next) => {
   var data = "";
   req.on("data", (chunk) => { data += chunk})
   req.on("end", () =>{
       req.body = qs.parse(data);
       next();
   })
})


app.all("/*", (req, res, next) => {
    if ("show_source" in req.query) {
        return res.end(fs.readFileSync(__filename));
    }
    if (req.path == "/") {
        return next();
    }

    var ip = req.connection.remoteAddress;
    var payload = "";
    for (var k in req.query) {
        if (waf(req.query[k])) {
            payload = req.query[k];
            break;
        }
    }
    for (var k in req.body) {
        if (waf(req.body[k])) {
            payload = req.body[k];
            break;
        }
    }

    if (payload.length > 0) {
        var sql = `INSERT INTO blacklists(ip, payload) VALUES(?, ?) ON DUPLICATE KEY UPDATE payload=?`;
    } else {
        var sql = `SELECT ?,?,?`;
    }
    
    return pool.query(sql, [ip, payload, payload], (err, rows) => {
        var sql = `SELECT * FROM blacklists WHERE ip=?`;
        return pool.query(sql, [ip], (err,rows) => {
            if ( rows.length == 0) {
                return next();
            } else {
                return res.end("Shame on you");
            }
            
        });
    });

});


app.get("/", (req, res) => {
    var sql = `SELECT * FROM blacklists GROUP BY ip`;
    return pool.query(sql, [], (err,rows) => {
        res.header("Content-Type", "text/html");
        var html = "<pre>Here is the <a href=/?show_source=1>source</a>, thanks to Orange\n\n<h3>Hall of Shame</h3>(delete every 60s)\n";
        for(var r in rows) {
            html += `${parseInt(r)+1}. ${rows[r].ip}\n`;

        }
        return res.end(html);
    });

});

app.post("/reg", (req, res) => {
    var username = req.body.username;
    var password = req.body.password;
    if (!username || !password || username.length < 4 || password.length < 4) {
        return res.end("Bye");
    } 

    password = crypto.createHash("md5").update(password).digest("hex");
    var sql = `INSERT INTO users(username, password) VALUES('${username}', '${password}') ON CONFLICT (username) DO NOTHING`;
    return client.query(sql.split(";")[0], (err, rows) => {
        if (rows && rows.rowCount == 1) {
            return res.end("Reg OK");
        } else {
            return res.end("User taken");
        }
    });
});

app.listen(31337, () => {
    console.log("Listen OK");
});


