var faker = require('../config/faker'),
    moment = require('moment');
var invalid_samples = [];


module.exports = {
    getValidSamples: function (comPanyId, productId, numberOfSamples) {
        var validSamples = [];
        for (let index = 0; index < numberOfSamples; index++) {
            validSamples.push({
                code : faker.internet.mac().substring(0,12),
                company_id : comPanyId,
                product_id : productId,
                analized : 0,
                h : faker.random.number(),
                pb_ss : faker.random.number(),
                fad_ss : faker.random.number(),
                fnd_ss : faker.random.number(),
                cz_ss : faker.random.number(),
                vrf_ss : faker.random.number(),
                gb_ss : faker.random.number(),
                lad_ss : faker.random.number(),
                almidon_ss : faker.random.number(),
                ac_acetico : faker.random.number(),
                ac_lactico : faker.random.number(),
                comment :faker.lorem.lines(2),
                created : moment(faker.date.recent()).format('YYYY-MM-DD HH:mm:ss')
            });
        }

        return validSamples;
    }
}



