module.exports = (module_name, file) => () => import(`@/modules/${module_name}/views/${file}.vue`)
