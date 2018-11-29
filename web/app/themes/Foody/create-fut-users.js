/**
 * Created by moveosoftware on 11/29/18.
 */

const fs = require('fs');
const async = require('async');
const {exec} = require('child_process');

let users = JSON.parse(fs.readFileSync('./fut_users.json', 'utf8'));


users = users.map((user) => {

    let names = user.name.split(' ');

    user.firstName = names.shift().trim();

    user.lastName = names.join(' ').trim();

    return user;
});


async.map(users, createWpUser, function (err, wpUsers) {

    if(err){
        console.log(err.message);
        return;
    }

    fs.writeFileSync('./wp_fut_users.json',JSON.stringify(wpUsers,null,2));

});


function createWpUser(user, cb) {


    let userPass = generatePass();

    let createUserCmd =
        `wp user create ${user.email} ${user.email}  --first_name='${user.firstName}'  --last_name='${user.lastName}' --display_name='${user.name}' --role=${user.role} --user_pass='${userPass}'`;


    exec(createUserCmd, (err, stdout, stderr) => {
        if (err) {
            cb(err);
            return;
        }

        user.password = userPass;

        cb(null, user);
    });
}

function generatePass() {
    let text = "";
    let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (let i = 0; i < 12; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

