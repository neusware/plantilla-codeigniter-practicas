import BaseApiCalls, { initBaseApiCalls } from './BaseApiCalls'

initBaseApiCalls()

class GroupsApi extends BaseApiCalls {
  constructor() {
    super('group')
  }
}

// export { GroupsApi as default }
export default new GroupsApi()
