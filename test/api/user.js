var BaseApi = require('../api/baseapi');

class User extends BaseApi {

  constructor(token) {
    super(token);
    this.controllerName = 'user';
  }
}

module.exports = User