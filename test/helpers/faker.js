var faker = require('faker');
faker.locale = "es";


faker.helpers.dateToUnix = function(dateString) {
    return parseInt((new Date(dateString)).getTime() / 1000)
}

module.exports = faker