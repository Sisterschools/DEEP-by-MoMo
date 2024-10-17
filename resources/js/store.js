import { reactive } from 'vue'

export const store = reactive({
  error: false,
  errorMsgHeader: '',
  errorMsgTxts: {},
  token: null,
  userData: null,
  isListComponent: false,
  hasNew: false,
  router: null,
  currentSchool: null
})