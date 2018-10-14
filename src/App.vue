<template>
    <v-app light>
      <v-btn :color="(tracking) ? 'success': ''" @click="tracking = !tracking">Tracking</v-btn>
      <h1 class="text-md-center">{{timeElapsed}}</h1>
      <v-layout row wrap>
        <v-flex xs3>
          <v-data-table :headers="trackHeaders" :items="tracks" class="elevation-1">
                <template slot="items" slot-scope="props" >
                  <td :class="colorOfWeek(props.item.start)">{{ props.item.start }}</td>
                  <td :class="colorOfWeek(props.item.start)">{{ props.item.end}}</td>
                  <td :class="colorOfWeek(props.item.start)">{{ props.item.delta }}</td>
                </template>
                <template slot="footer" >
                  <div class="ma-3"><strong>Total: </strong><span>{{totalHours}}</span></div>
                </template>
          </v-data-table>
        </v-flex>
        <v-flex xs5 offset-xs1>
          <v-data-table :headers="taskHeaders" :items="tasks" class="elevation-1">
                <template slot="items" slot-scope="props" >
                  <td :class="{'light-green lighten-5': props.item.done}">{{ props.item.n }}</td>
                  <td :class="{'light-green lighten-5': props.item.done}">{{ props.item.name }}</td>
                  <td :class="{'light-green lighten-5': props.item.done}">{{ props.item.description }}</td>
                  <td :class="{'light-green lighten-5': props.item.done}">
                    <v-btn icon small @click="activeTask = props.item; showDialog = true">
                      <v-icon small>create</v-icon>
                    </v-btn>
                    <v-btn icon small>
                      <v-icon small color="success" @click="props.item.done = !props.item.done">done</v-icon>
                    </v-btn>
                    <v-btn icon small>
                      <v-icon small color="error" @click="deleteTask(props.item)">delete</v-icon>
                    </v-btn>
                  </td>
                </template>
                <template slot="footer" >
                  <v-btn flat @click="activeTask = {n: (tasks.length) ? tasks[tasks.length - 1].n + 1 : 1, done: false}; showDialog = true; newTask = true">Add task</v-btn>
                </template>

          </v-data-table>
        </v-flex>
      </v-layout>
      <v-dialog v-model="showDialog" width="400"  @keydown.esc="showDialog = false">
        <v-card >
          <v-card-title class="primary white--text"><h3>Task</h3></v-card-title>
          <div class="px-3">
            <v-text-field v-model="dialog.name" label="Task name" ></v-text-field>
            <v-textarea v-model="dialog.description" label="Task description" ></v-textarea>
          </div>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="primary" flat @click="addTask" v-if="newTask"> Добавить </v-btn>
            <v-btn color="primary" flat @click="activeTask.name = dialog.name; activeTask.description = dialog.description; save(); showDialog = false" v-else> Сохранить </v-btn>
            <v-btn color="primary" flat @click="showDialog = false" > Закрыть </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-app>
</template>

<script>
import HelloWorld from './components/HelloWorld'
import * as moment from 'moment'

export default {
  name: 'App',
  data: () => ({
    newTask: false,
    showDialog: false,
    tray: null,
    taskHeaders: [
      { text: '№', value: 'n', sortable: false },
      { text: 'Task name', value: 'name', sortable: false },
      { text: 'Description', value: 'description', sortable: false },
      { text: 'Actions', value: 'actions', sortable: false },
    ],
    trackHeaders: [
      { text: 'Start', value: 'start' },
      { text: 'End', value: 'end' },
      { text: 'Duration', value: 'delta' },
    ],
    dialog: {},
    drawer: false,
    activeTask: {},
    tracking: false,
    timeElapsed: '00:00',
    tracks: [],//[{"start": "14-10 01:21", "end": "14-10 01:22", "delta": null}, { "start": "14-10 01:35", "end": "14-10 01:36", "delta": "0.0" }],
    tasks: [],//[{'name': 'name', 'description': 'description', 'done': false}],
    startTracking: ''
  }),
  components: {
    HelloWorld
  },
  computed: {
    totalHours () {
      let total = 0
      this.tracks.map((el) => { total += parseFloat(el.delta) })
      return total.toFixed(1)
    }
  },
  methods: {
    save () {
      const params = {
        tasks: this.tasks,
        tracks: this.tracks
      }
      this.$http.post('http://prestashop.local/timetracker/tools.php', params).then((res) => {
        console.log(res.data)
      })
    },
    deleteTask (task) {
      this.tasks = this.tasks.filter( el => el.name != task.name )
    },
    addTask () {
      this.activeTask.name = this.dialog.name
      this.activeTask.description = this.dialog.description
      //this.activeTask.done = false
      this.newTask = false
      this.tasks.unshift(this.activeTask)
      this.showDialog = false
    },
    day(str) {
      return moment(str, 'DD-MM HH:mm').day()
    },
    colorOfWeek (str) {
      const n = moment(str, 'DD-MM HH:mm').day()
      switch (n) {
        case 0: return 'orange lighten-5'
        case 1: return 'purple lighten-5'
        case 2: return 'deep-purple lighten-5'
        case 3: return 'indigo lighten-5'
        case 4: return 'blue lighten-5'
        case 5: return 'teal lighten-5'
        case 6: return 'lime lighten-5'
      }
    }

  },
  watch: {
    showDialog (val) {
      if (!val) {
        this.newTask = false
      } else {
        this.dialog.name = this.activeTask.name
        this.dialog.description = this.activeTask.description
      }
    },
    tracking (val) {
      if (val) {
        this.startTracking = moment().format('DD-MM HH:mm')
      } else {
        this.tracks.push({
          start: this.startTracking,
          end: moment().format('DD-MM HH:mm'),
          delta: moment().diff(moment(this.startTracking, 'DD-MM HH:mm'), 'hours', true).toFixed(1)
        })
      }
    },
    tasks () {
        this.save()
    },
    tracks () {
      this.save()
    }
  },
  mounted () {
    this.$http.get('http://prestashop.local/timetracker/tools.php').then((res) => {
      this.tracks = res.data.tracks
      this.tasks = res.data.tasks
    })
    this.interval = setInterval(() => {
      if (this.tracking) {
        this.timeElapsed  = moment(this.timeElapsed, 'HH:mm').add(1, 'm').format('HH:mm')
      }
    }, 60000)
  }
}
</script>

<style>
</style>
