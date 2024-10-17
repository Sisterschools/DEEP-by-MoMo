import { store } from './store.js';

var server = ( uri, vars, method = 'POST', token, contentType = 'application/x-www-form-urlencoded', putAsPost = false) => {

  var headers = {
    "Accept": "application/json",
  } 

  if(contentType !== 'multipart/form-data')
    headers['Content-Type'] = contentType

  if( token ){
    headers["Authorization"] = "Bearer " + token
  }

  var body = new URLSearchParams(vars)

  if(contentType == 'multipart/form-data'){
    body = new FormData()
    for(var i in vars){
      if(vars[i] instanceof Array)
        vars[i].forEach( (n, m) => body.append(i + '[]', vars[i][m]))
      else
        body.append(i, vars[i])
    }
    if(putAsPost)
      body.append('_method', 'PUT')
  }

  if( (method == 'POST' || method == 'PUT') && contentType == 'application/json'){
    body = JSON.stringify( vars )
  }

  return fetch( uri, { 
    method,
    headers,
    ...((method == 'POST' || method == 'PUT') && {body}) 
  } )
  .then( ( response ) => {
    if( ! response.ok ){
      return Promise.reject(response.json())
    }
    if(response.ok && response.status < 300)
      return response
  })
  .then( ( response ) => response.json().catch(() => {} ))
}

var makeErrorMsg = (response) => {
  if(response && response.then){
    response.then( ( e ) => {
      store.errorMsgHeader = e.message || 'Error'
      store.errorMsgTxts = e.errors
      store.error = true
    })
    .catch( () => {
      store.errorMsgHeader = 'No Internet ?'
      store.error = true      
    })
  }
  else if(typeof response == 'string'){
    store.errorMsgHeader = ''
    store.errorMsgTxts = response
    store.error = true
  }
  else{
    store.errorMsgHeader = 'No Internet?'
    store.error = true
    console.log(response)
    store.errorMsgTxts = response
  }
}

export { server, makeErrorMsg }