var faker = require('../helpers/faker');

module.exports = {
    getValidUsers: function(number) {
        var validUsers = [];
        for (let index = 0; index < 20; index++) {
            name = faker.hacker.noun();
            validUsers.push(
                {
                    address: faker.address.streetAddress(true),
                    first_name: faker.name.firstName(),
                    last_name: faker.name.lastName(),
                    phone: faker.phone.phoneNumber(),
                    email: faker.internet.email(),
                    password: faker.internet.password(),
                    roles: 1
                }
            );
        }

        return validUsers;        
    },
    
    getInvalidUsers: function() {
        name = faker.hacker.noun();
        var validUsers = [
            {
                address: faker.address.streetAddress(true),
                first_name: faker.name.firstName(),
                last_name: faker.name.lastName(),
                phone: faker.phone.phoneNumber(),
                email: "superadmin@signlab.es",
                password: faker.internet.password(),
                roles: 1
            }
        ];

        return validUsers;        
    }
}

