/**
 * ROOT COMPONENT
 */
var app = new Vue({
  el: '#app-wall',
  data: {
    appTitle: 'VueDown Editor',
    code: '# Hello dev \n\n---\n\nType **here** some markdown! ',
    isSeen: true,
    isSettings: false,
    isFull: false,
    isUpdating: false,
    actualSkin: {
      color: '#0cc',
      background: 'rgba(0, 204, 204, .4)',
      wall: 'rgba(0, 204, 204, .15)'
    },
    skins: [
      {
        color: '#0cc', 
        background: 'rgba(0, 204, 204, .4)',
        wall: 'rgba(0, 204, 204, .15',
        isActive: true
      },
      {
        color: '#0c0', 
        background: 'rgba(0, 204, 0, .4)',
        wall: 'rgba(0, 204, 0, .15',
        isActive: false
      },
      {
        color: '#f60',
        background: 'rgba(255, 102, 0, .4)',
        wall: 'rgba(255, 102, 0, .15)',
        isActive: false
      },
      {
        color: '#f0f',
        background: 'rgba(255, 0, 255, .4)',
        wall: 'rgba(255, 0, 255, .15)',
        isActive: false
      },
      {
        color: '#e00',
        background: 'rgba(204, 0, 0, .2)',
        wall: 'rgba(204, 0, 0, .15)'
      }
    ]
  },
  computed: {
    compiledOutput(){
      return marked(this.code, {sanitize: true});
    }  
  },
  methods: {
    showSettings(){
      this.isSettings = ! this.isSettings;
      this.isSeen = false;
    },
    setSkin(skin){
      this.actualSkin.color = skin.color;
      this.actualSkin.background = skin.background;
      this.actualSkin.wall = skin.wall;
      // Toggle the actual active skin
      this.skins.map(function(obj){
        if(obj === skin){
          obj.isActive = true;
        }
        else{
          obj.isActive = false;
        }
      })
    },
    updateMe(){
      var ctx = this;
      ctx.isUpdating = true;
      setTimeout(function(){
        ctx.isUpdating = false;
      }, 4000);
    }
  }
});