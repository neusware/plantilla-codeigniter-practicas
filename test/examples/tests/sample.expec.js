var chai = require('chai'),
	api = require('../api/base.js'),
	expect = chai.expect,
	mocksSample = require('../mocks/sample'),
	mocksProduct = require('../mocks/product'),
	mocksCompany = require('../mocks/company'),
    faker = require('../config/faker');
	
//Sample
describe('/sample', function () {
	let sampleId;
    let companyId;
	let productId;

	before(function() {
        // runs before all tests in this file regardless where this line is defined.
    });

	// Sample depends of product and company
	it('POST - create a new product', function (done) {
		api.createElement('product',
			mocksProduct.getValidProducts(1)[0],
			function (res, done) {
				productId = res.body.data.id;
				done();
			},
			done
		);
	});

	it('POST - create a new company', function (done) {
		api.createElement('company',
			mocksCompany.getValidCompanies(1)[0],
			function (res, done) {
				companyId = res.body.data.id;
				done();
			},
			done
		);
	});

	it(`GET list of samples`, function (done) {
		api.getAllElements('sample', done);
    });
	
	it(`GET paginated samples`, function (done) {
		api.getPaginatedElements('sample', 1, done);
	});

	it(`GET dropdown samples`, function (done) {
		api.dropdownElements('sample', null, done);
	});
	
	describe('Basic sample CRUD', function () {
		
		it('POST - create a new sample', function (done) {
			api.createElement('sample',
			mocksSample.getValidSamples(companyId, productId, 1)[0],
				function (res, done) {
					sampleId = res.body.data.id;
					done();
				},
				done
			);
		});

		it('POST - update a sample', function (done) {
			var updatedSample = mocksSample.getValidSamples(companyId, productId, 1)[0];
			updatedSample.id = sampleId;
			api.updateElement('sample',updatedSample,null,done);
		});
	
		it('DELETE a single sample', function (done) {
			api.deleteElement('sample',sampleId,null,done);
		});
	});
	
	describe('Creating control sample', function () {
		let companyId2;
		it('POST - create other example company', function (done) {
			api.createElement('company',
				mocksCompany.getValidCompanies(1)[0],
				function (res, done) {
					companyId2 = res.body.data.id;
					done();
				},
				done
			);
		});

		it('POST - create a new control sample', function (done) {
			let sample = mocksSample.getValidSamples(null, productId, 1)[0];
			sample.control_companies = [companyId, companyId2];
			api.createElement('sample',
			sample,
				function (res, done) {
					sampleId = res.body.data.id;
					done();
				},
				done
			);
		});
	});
});
