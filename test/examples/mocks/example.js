var faker = require('../config/faker');

module.exports = {
    getValidExamples: function(number) {
        var validExamples = [];
        for (let index = 0; index < 20; index++) {
            name = faker.hacker.noun();
            validExamples.push(
                { 
                    name: name, 
                    code: name.substring(0,2).toUpperCase()
                }
            );
        }

        return validExamples;        
    },

    // TO-DO Invalid Examples
    getInvalidExamples: function(number) {
        var invalidExamples = [];
        invalidExamples.push(
            { 
                name: null, 
                code: name.substring(0,2).toUpperCase()
            }
        );
        
        invalidExamples.push(
            { 
                name: name, 
                code: null
            }
        );

        return invalidExamples;
    }
}

