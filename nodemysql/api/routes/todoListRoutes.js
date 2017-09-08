'use strict';
module.exports = function(app) {
  var todoList = require('../controller/todoListController');

  // todoList Routes
  app.route('/user-list')
    .get(todoList.list_all_tasks);


  
};