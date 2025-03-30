var faker = require('../config/faker');

module.exports = {
    getValidLaboratories: function(number) {
        var validLaboratories = [];
        for (let index = 0; index < 20; index++) {
            name = faker.hacker.noun();
            validLaboratories.push(
                { 
                    name: faker.company.companyName(), 
                    address: faker.address.streetAddress(),
                    cif: faker.random.uuid().substring(0,10) ,
                    phone: faker.phone.phoneNumber().toString()
                }
            );
        }

        return validLaboratories;        
    }
}

