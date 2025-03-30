var faker = require('../config/faker');

module.exports = {
    getValidCompanies: function(number) {
        var validCompanies = [];
        for (let index = 0; index < 20; index++) {
            name = faker.hacker.noun();
            validCompanies.push(
                { 
                    name: name, 
                    code: name.substring(0,2).toUpperCase()
                }
            );
        }

        return validCompanies;        
    }
}

