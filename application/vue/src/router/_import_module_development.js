module.exports = (module_name, file) => require(`@/modules/${module_name}/views/${file}.vue`).default // vue-loader at least v13.0.0+
