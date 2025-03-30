var faker = require('../config/faker');

module.exports = {
    getValidProducts: function(number) {
        var validProducts = [];
        for (let index = 0; index < 20; index++) {
            name = faker.hacker.noun();
            validProducts.push(
                { 
                    name: name, 
                    code: name.substring(0,2).toUpperCase()
                }
            );
        }

        return validProducts;        
    }
}

