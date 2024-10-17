var dateFormat = ( str, opts ) => {
  var d = new Date(str)
  if( d ){
    var opts = opts || 
      {
        year: 'numeric', 
        month: 'short', 
        day:'numeric', 
        hour:'numeric', 
        minute: 'numeric'
      }
    return d.toLocaleDateString('en-EN', opts)
  }
}

export { dateFormat }