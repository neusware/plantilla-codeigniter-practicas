var chai = require('chai'),
	config = require('./config.js'),
	expect = chai.expect,
    chaiHttp = require('chai-http');

chai.use(chaiHttp);

var request = chai.request(config.baseUrl);

var loginData = {
    admin: {
        identity: 'superadmin@signlab.es',
        password: '123Signlab.'
    }
}

module.exports = {
    user: 'admin',
    login: function(callBack,done) {
        request
        .post(`/external/login`)
        .type('form')
        .send(loginData[this.user])
        .end( function (err, res) {
            if(res.status != 200) {
                console.log(res.text);
            }
            expect(res).to.have.status(200);
            expect(res).to.be.json;
            expect(res.body).to.not.be.empty;
            expect(res.body.data).to.have.own.property('token');
            if(callBack != null) callBack(res, done);
			else done();
        });
    }
}