var chai = require('chai'),
	api = require('../api/base.js'),
	companyMocks = require('../mocks/company'),
	expect = chai.expect;
	
//Company
describe('/company', function () {
	let companyId;
	
	it(`GET list of companys`, function (done) {
		api.getAllElements('company', done);
	});

	it('POST - create a new company', function (done) {
		api.createElement('company',
		companyMocks.getValidCompanies(1)[0],
			function (res, done) {
				companyId = res.body.data.id;
				done();
			},
			done
		);
	});

	it('POST - update a company', function (done) {
		let company = companyMocks.getValidCompanies(1)[0];
		company.id = companyId;
		api.updateElement('company',company,null,done);
	});

	it('DELETE a single company', function (done) {
		api.deleteElement('company',companyId,null,done);
	});
});