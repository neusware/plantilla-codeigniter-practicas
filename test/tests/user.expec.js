var chai = require('chai'),
	User = require('../api/user'),
	apiLogin = require('../api/login'),
	userMocks = require('../mocks/user'),
	expect = chai.expect;
	
//Center
describe('/user', function () {
	let userId;
	let user;

	it(`Login`, function (done) {
		apiLogin.login(function (res, done) {
			user = new User(res.body.data.token);
			done();
		},
		done);
	});
	
	
	it(`GET list of users`, function (done) {
		user.getAllElements(done);
	});

	it('POST - create a new user', function (done) {
		user.createElement(
			userMocks.getValidUsers(1)[0],
			function (res, done) {
				userId = res.body.data.id;
				done();
			},
			done
		);
	});

	it('POST - update a user', function (done) {
		let userTemp = userMocks.getValidUsers(1)[0];
        userTemp.id = userId;
		user.updateElement(userTemp,null,done);
	});

	it('DELETE a single user', function (done) {
		user.deleteElement(userId,null,done);
	});


	/* Negative Testing */

	it('POST - BAD create a new user', function (done) {
		user.createBadElement(
			userMocks.getInvalidUsers()[0],
			function (res, done) {
				userId = res.body.data.id;
				done();
			},
			done
		);
	});
	
});
