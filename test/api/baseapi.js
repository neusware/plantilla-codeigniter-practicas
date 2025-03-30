var chai = require('chai'),
	config = require('./config.js'),
	expect = chai.expect,
	chaiHttp = require('chai-http');
	
chai.use(chaiHttp);

class BaseApi {

  constructor(token) {
    this.request = chai.request(config.baseUrl);
    this.api_token = token
  }

  nullToUndefinedHelper(data) {
    for(var propertyName in data) {
      if(data[propertyName] == null) {
        data[propertyName] = undefined;
      }
    }
  }

  basicCheckResponse(res) {
    if(res.status != 200) {
      console.log(res.text);
    } else if(res.body.code != 20000 && res.body.code != 20001) {
      console.log(res.text);
    }
    expect(res).to.have.status(200);
    expect(res).to.be.json;
    expect(res.body).to.not.be.empty;
  }

  basicCheckInvalidResponse(res) {
    if(res.status != 200) {
      console.log(res.text);
    } else if(res.body.code != 40000 && res.body.code != 40001) {
      console.log(res.text);
    }
    expect(res).to.have.status(200);
    expect(res).to.be.json;
    expect(res.body).to.not.be.empty;
  }

  getAllElements(done) {
		let self = this;
		this.request
		.get(`/${this.controllerName}/all`)
		.set('X-Token', this.api_token)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			done();
		});
  }
  
  createElement(args, callBack, done) {
		let self = this;
		self.nullToUndefinedHelper(args);
		this.request
		.post(`/${this.controllerName}/create`)
		.set('X-Token', this.api_token)
		.type('form')
		.send(args)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			expect(res.body.data.id).to.be.a("number");
			// Check that is created
			self.getSingleElement(res.body.data.id, callBack, done);
		});
  }
  
  getSingleElement(elementId,callBack,done) {
		let self = this;
		this.request
		.get(`/${this.controllerName}/data/` + elementId)
		.set('X-Token', this.api_token)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			if(callBack != null) callBack(res, done);
			else done();
		});
  }
  
  updateElement(args,callBack,done) {
		let self = this;
		self.nullToUndefinedHelper(args);
		this.request
		.post(`/${this.controllerName}/update`)
		.set('X-Token', this.api_token)
		.send(args)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			self.getSingleElement(args.id,function(res,done) {
				for(var propertyName in args) {
					if(res.body.data[propertyName]){
						expect(res.body.data[propertyName].toString()).to.equal(args[propertyName].toString())
					}
				}
				if(callBack != null) callBack(res, done);
				else done();
			},done);
	
		});
  }
  
  deleteElement(elementId,callBack,done) {
		let self = this;
		this.request
		.post(`/${this.controllerName}/delete`)
		.set('X-Token', this.api_token)
		.send({id: elementId})
		.end( function (err, res) {
			self.basicCheckResponse(res);
			// Check that is deleted
			self.getSingleElement(elementId,function(res,done) {
				expect(res.body.data).to.be.null;
				if(callBack != null) callBack(res, done);
				else done();
			},done);
		});
  }
  
  getPaginatedElements(page, done) {
		this.request
		.get(`/${this.controllerName}/page/${page}`)
		.set('X-Token', this.api_token)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			expect(res.body.data.counts).to.be.an('object');
			expect(res.body.data.data).to.be.an('array');
			done();
		});
  }
  
  dropdownElements(allBack,done) {
		this.request
		.get(`/${this.controllerName}/dropdown/`)
		.set('X-Token', this.api_token)
		.end( function (err, res) {
			self.basicCheckResponse(res);
			if(callBack != null) callBack(res, done);
			else done();
		});
  }
  

  /* Negative Testing */

  createBadElement(args, callBack, done) {
		let self = this;
		self.nullToUndefinedHelper(args);
		this.request
		.post(`/${this.controllerName}/create`)
		.set('X-Token', this.api_token)
		.type('form')
		.send(args)
		.end( function (err, res) {
			self.basicCheckInvalidResponse(res);
			expect(res.body.data.message).to.be.a("string");
			expect(res.body.code).to.equal(40000);
			if(callBack != null) callBack(res, done);
		});
  }
}

module.exports = BaseApi