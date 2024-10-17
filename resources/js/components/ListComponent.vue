<script setup>
  import { RouterLink } from 'vue-router';
  import { store } from '../store.js'
  import { dateFormat } from '../dateFormat.js';

  defineProps({
    entity: {type:String, default: ''},
    columns: {type:Object, required:true, default: new Object},
    selectableRows: {type: Boolean, required: false, default: false},
    showHeader: {type: Boolean, required: false, default: true},
    shiftClick: {type: Boolean, required: false, default: false},
    showIDColumn: { type: Boolean, required: false, default: false}, 
    items: {type: Array, required: true, default: new Array},
    caption: {type: String, required: true, default: 'List'},
    captionIsFoldable: {type: Boolean, required:false, default: false},
    selectColumns: {type: Boolean, required:false, default: false}
  })
</script>

<script>
export default{
  emits:['delete', 'rowclick'],
  data(){
    return {
      showItems: [],
      expanded: false,
      itemsHaveValidIds: true,
      selectedIds: [],
      prevSelected: null,
      transscribe: [],
      subItems: [],
      visibleColumns: [],
      id: 'id'
    }
  },
  computed:{
    parseItems: function(){
      var newItems = []

      this.items.forEach( ( item ) => {
        var newItem = {}
        Object.keys(this.columns).reverse().forEach((j) => {
          if(this.columns[j]['as'])
            this.transscribe.push([j, this.columns[j]['as']])
          if(this.columns[j]['subItem'])
            this.subItems.push(this.columns[j]['subItem'])
          for(var i in item){
            if( i == j ){
              if(this.columns[j]['type'] == 'date'){
                item[j]  = dateFormat(this.columns[j], this.columns[j]['params'])
              }
              newItem = Object.assign({ [i]: item[j] }, newItem)
            }
          }
        })
        newItems.push(newItem)
      })
      return newItems
    },
    filtered: function(){
      var objs = this.showItems.map( ( obj ) => {
        var o = Object.assign({}, obj)
        this.subItems.forEach( ( si ) => {
          for(var i in si){
            var rpl = si[i].split('.')
            rpl = rpl[1].replaceAll("'", "")
            this.showItems.forEach( ( item ) => {
              if(item[i.toString()] && item[i.toString()][rpl])
                item[i] = item[i.toString()][rpl]
            })
          }
        })
        return o
      })
      return objs
    },
    filtered0: function(){
      var objs = Object.keys(this.showItems[0]),
        items = {}
      objs.forEach((k) => {
        items[k] = k
      })
      this.transscribe.forEach( ( tr ) => {
        var o = objs.findIndex(( q ) => q == tr[0])
        if(o > -1)
          items[tr[0]] = tr[1]
      })
      return items
    }
  },
  beforeMount(){
    Object.keys(this.columns).forEach( (key, n) => { 
      if( typeof this.columns[key]['visible'] == 'undefined' || 
        this.columns[key]['visible'] == true)
        this.visibleColumns.push(n)
      if( this.columns[key]['type'] == 'id')
        this.id = key
    })
    this.items.forEach( ( obj ) => {
      if( typeof obj[this.id] == 'undefined') this.itemsHaveValidIds = false
    })
  },
  mounted(){
    store.isListComponent = true
  },
  
  unmounted(){
    store.isListComponent = false;
  },
  methods:{
    hideBulkActions(){
      var el = this.$el.querySelector('div.dropdown')
      if(el && ! el.classList.contains('hide'))
        el.classList.add('hide')
    },
    toggleBulkActions(){
      var el = this.$el.querySelector('div.dropdown')
      el.classList.toggle('hide')
    },
    toggleFolded(){
      this.$el.querySelector('.expand').classList.toggle('hide')
      this.$el.querySelector('.expandable').classList.toggle('expanded')
      this.expanded =! this.expanded
      if( ! this.expanded )
        this.hideBulkActions()
    },
    toggleDropdown(){
      var el = this.$el.querySelector('ul.dropdown'),
        icon = this.$el.querySelector('.expandable')
      el.classList.toggle('hide')
      icon.classList.toggle('expanded')
    },
    classIfNotVisibleColumn(key){
      var i = Object.keys(this.columns).indexOf(key),
        min = Math.min(...this.visibleColumns),
        typeCls = this.columns[key]['type'] || ''

      if(i == min)
        return typeCls +' cell firstVisible'
      else if(this.visibleColumns.indexOf(i) == -1)
        return 'hide'
      return typeCls + ' cell'
    },
    tableClasses(){
      return 'table ' + (this.selectableRows && this.itemsHaveValidIds ? ' selectableRows' : '')
    },
    deleteClick(){
      this.$emit('delete', Object.values(this.selectedIds))
    },
    onShiftClick(evnt){
      if(evnt.target.nodeName == 'INPUT'){
        var cell = evnt.target.closest('.row').querySelector('.cell'),
        selId = cell.getAttribute('data-src')
        if(evnt.shiftKey && selId ){
          var val = parseInt(selId)
          this.selectedIds.indexOf(val) == -1 ? this.selectedIds.push(val) : ''
          this.setPrevRowSelected(evnt.target)
        }
        else if(selId)
          this.prevSelected = selId
      }
    },
    setPrevRowSelected( rowEl ){
      if( ! rowEl || ! rowEl.closest ) return
      var prevRowEl = rowEl.closest('.row').previousSibling,
        prevCell = prevRowEl && prevRowEl.querySelector ? prevRowEl.querySelector('.cell') : null,
        selId = prevCell ? prevCell.getAttribute('data-src') : ''
      if( selId && selId != this.prevSelected && this.selectedIds.indexOf(selId) == -1)
         this.selectedIds.push(parseInt(selId))
      else if(selId) return
      this.setPrevRowSelected( prevRowEl )
    },
    onRowClick( o ){
      var id = 
        o.target.closest('.row')
        .querySelector('div:not(.actions)')
        .getAttribute('data-src')
      if(id)
        this.$emit('rowclick', id)
    },
    selectOrDeselectAll( evnt ){
      if(! evnt.target.checked) 
        this.selectedIds = []
      else{
        this.showItems.forEach(( i ) => {
          this.selectedIds.push(i[this.id])
        })
      }
    },
    verticalDotsClass(){
      var cls = 'unvisible'
      if(this.selectableRows && (! this.captionIsFoldable || this.expanded) && this.selectedIds.length > 0)
        cls = 'vertical-dots'
      this.expanded ? cls += ' white' : null

      return cls
    }
  }
}
</script>

<template>
  <div>
    <div 
      class="header1"
    >
      <div>
        <div class="dropdown hide">
          <span @click="deleteClick">- Delete</span>
        </div>
        <p
          :class="captionIsFoldable ? 'bg-blue white' : ''" 
          @click.stop="toggleFolded"
        >
          <span 
            v-if="captionIsFoldable" 
            class="expandable white" 
          />
          <span 
            :class="verticalDotsClass()"
            @click.stop="toggleBulkActions"
          >
            &nbsp;
            &vellip;
            &nbsp;
          </span>
          {{ caption }}
          <span v-if="captionIsFoldable">&nbsp;</span>
          <span class="right ">
            &nbsp;&nbsp;
            <RouterLink 
              class="white no-underline" 
              :to="{path: '/' + entity + '/add'}"
            >
              &#10010;
            </RouterLink>
            &nbsp;&nbsp;
          </span>
        </p>
        <div>
          <ul v-if="selectColumns">
            <li
              @click="toggleDropdown"
            >
              <span class="expandable" />
              Columns
            </li>
            <li>
              <ul class="dropdown hide">
                <li
                  v-for="(n, i) in Object.keys(columns)" 
                  :key="n"
                >
                  <label>
                    <input 
                      v-model="visibleColumns"
                      type="checkbox"
                      :value="i"
                    > 
                    {{ n }}
                  </label>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div 
      :class="captionIsFoldable ? 'hide expand': ''"
    >
      <p v-if="(showItems = parseItems) && showItems.length == 0">
        Nothing here yet: create one 
        <RouterLink :to="{path: '/' + entity + '/add'}">
          here
        </RouterLink>
      </p>
      <div 
        v-else
        :class="tableClasses()"
      >
        <div 
          v-if="showItems && showItems[0]"
          :class="showHeader ? 'row ' + (captionIsFoldable ? 'row bg-white' : '' ) : 'hide'" 
        >
          <div 
            v-if="selectableRows && itemsHaveValidIds"
            class="actions"
          >
            <input
              type="checkbox" 
              class="selectAll"
              @change="selectOrDeselectAll"
            >
          </div>
          <div
            v-for="(value, key) in filtered0"
            :key="key"
            :class="classIfNotVisibleColumn(key)" 
          >
            {{ value }}
          </div>
        </div>
        <div  
          v-for="(item, n) in filtered"
          :key="item"
          class="row" 
        >
          <div 
            v-if="selectableRows && itemsHaveValidIds"
            class="actions"
          >
            <input 
              v-model="selectedIds"
              :value="items[n][id]"
              type="checkbox"
              @click="onShiftClick"
            >
          </div>
          <div 
            v-for="(prop, index) in item"
            :key="prop"
            :data-src="items[n][id]"
            :class="classIfNotVisibleColumn(index)"
            @click.stop="onRowClick"
          >
            <img 
              v-if="columns[index]['type'] == 'img' && prop"
              :src="prop"
              alt="image" 
            >
            <span v-else>
              {{ prop }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.vertical-dots{
  cursor: pointer;
  float: left;
}
.dropdown{
  cursor: pointer;
  position:absolute;
  margin: 1em 4em;
  padding: 0.5em 0.5em;
  border: 2px solid var(--momo-blue);
  background-color: var(--momo-white);
}
.header1 div{
  box-sizing: border-box;
}
.header1 > div:nth-child(2){
  width: 100%;
}
.header1 select{
  float: left;
  position: relative;
  top:1em;
  padding: 0.5em 0.5em;
  min-width: 12em;
  max-width: 12em;
}
#app .header1 ul{
  cursor: pointer;
  position: absolute;
  background-color: var(--momo-white);
  right: 0.5em;
  margin-top: 1em;
  z-index: 99;
}
#app .header1 ul ul{
  border: 2px solid var(--momo-blue);
}
#app .header1 ul li{
  padding-top: 0;
}
#app .header1 ul li input{
  display: inline;
  width: 2em;
  accent-color: var(--momo-blue);
  color: var(--momo-white);
}
.header1 > div:nth-child(1){
  width: 100%;
}
.header1 > div:nth-child(2){
  width: 100%;
  position: relative;
  left: -12em; 
}
p{
  text-align: center;
  width: calc(100% - 0.5em);
  float:left;
}
.dropdown{
  width: 12em;
}

</style>