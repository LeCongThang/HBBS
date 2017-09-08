'use strict';
var con = require('../models/connect');
exports.list_all_tasks = function(req, res) {

  con.query("SELECT * FROM ppc_district", function (err, result, fields) {
    if (err) throw err;
    res.json(result);
 
});
}