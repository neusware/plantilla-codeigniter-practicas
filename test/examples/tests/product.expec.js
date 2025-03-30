var chai = require('chai'),
	api = require('../api/base.js'),
	productMocks = require('../mocks/product'),
	expect = chai.expect;
	
//Product
describe('/product', function () {
	let productId;
	
	it(`GET list of products`, function (done) {
		api.getAllElements('product', done);
	});

	it('POST - create a new product', function (done) {
		api.createElement('product',
			productMocks.getValidProducts(1)[0],
			function (res, done) {
				productId = res.body.data.id;
				done();
			},
			done
		);
	});

	it('POST - update a product', function (done) {
		let product = productMocks.getValidProducts(1)[0];
		product.id = productId;
		api.updateElement('product',product,null,done);
	});

	it('DELETE a single product', function (done) {
		api.deleteElement('product',productId,null,done);
	});
});