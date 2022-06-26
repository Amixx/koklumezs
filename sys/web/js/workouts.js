Vue.component('modal', {
    name: 'modal',
    props: {
        id: {
            type: Number,
            required: true,
        },
        title: {
            type: String,
            required: false,
        }
    },
    template: `
    <div class="modal fade" :id="props.id" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="false">&times;</span>
                    </button>
                    <h3 class="modal-title" id="modal-title">{{ props.title }}</h3>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
    `
})

new Vue({
    data: function(){
        return {
            exercises: null,
            exerciseToAdd: null,
            addExerciseModal: {
                weight: null,
                reps: null,
            },
            workout: {
                studentId: null,
                exercises: []
            }
        }
    },
    created(){
        this.loadExercises()
        this.workout.studentId = window.studentId;
    },
    methods: {
        loadExercises(){
            axios.get('/fitness-exercises/api-list').then(res => {
                this.exercises = res.data
            })
        },
        initAddToWorkout(exercise){
            this.exerciseToAdd = exercise
            this.$nextTick(() => {
                $('#add-exercise-modal').modal('show')
            })
        },
        cancelAddingExercise(){
            this.closeAddExerciseModal();
        },      
        finishAddingExercise(){
            this.workout.exercises.push({
                ...this.exerciseToAdd,
                weight: parseFloat(this.addExerciseModal.weight),
                reps: this.addExerciseModal.reps,
            })
            this.cancelAddingExercise()
        },
        closeAddExerciseModal(){
            $('#add-exercise-modal').modal('hide');
            this.exerciseToAdd = null;
            this.addExerciseModal = {
                weight: null,
                reps: null,
            }
        },
        submitWorkout(){
            axios.post('/fitness-workouts/api-create', this.workout, {
                headers: {
                    "Content-type": "application/json",
                    'X-CSRF-Token': this.getCsrfToken()
                }
            })
        },
        getCsrfToken(){
            return document.querySelector("meta[name=csrf-token]").content
        }
    },
    template: `
    <div>
        <div class="row">
            

            <div class="col-md-6">
                <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="exercises-tab" data-toggle="tab" href="#exercises" role="tab" aria-controls="exercises" aria-selected="false">
                            Vingrinājumi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="templates-tab" data-toggle="tab" href="#templates" role="tab" aria-controls="templates" aria-selected="false">
                            Šabloni
                        </a>
                    </li>
                </ul>

                <div class="tab-content" id="exercise-tab-content">
                    <div class="tab-pane fade active in" id="exercises" role="tabpanel" aria-labelledby="exercises-tab">
                        <ul v-if="exercises" class="list-group">
                            <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item">
                                <span> {{ exercise.name }}</span>
                                <button class="btn btn-primary" @click="initAddToWorkout(exercise)">Pievienot treniņam</button>
                            </li>
                        </ul>
                    </div>
                     <div class="tab-pane fade active in" id="templates" role="tabpanel" aria-labelledby="templates-tab">
                        <ul v-if="exercises" class="list-group">
                            <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item">
                                <span> {{ exercise.name }}</span>
                                <button class="btn btn-primary" @click="initAddToWorkout(exercise)">Pievienot treniņam</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="workout.exercises.length" class="col-md-6">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nr.</th>
                            <th>Vingrinājums</th>
                            <th>Svars</th>
                            <th>Reizes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr  v-for="(exercise, i) in workout.exercises" :key="i">
                            <td>{{ i+1 }}</td>
                            <td>{{ exercise.name }}</td>
                            <td>{{ exercise.weight }}</td>
                            <td>{{ exercise.reps }}</td>
                        </tr>
                    </tbody>
                </table>

                <button class="btn btn-primary" @click="submitWorkout">Nosūtīt treniņu</button>
            </div>
        </div>
     

        <div class="modal fade" id="add-exercise-modal" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document" v-if="exerciseToAdd">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h3 class="modal-title" id="modal-title">Pievienot vingrinājumu {{ exerciseToAdd.name }}</h3>
                    </div>
                    <div class="modal-body">
                        <form>
                            <label>
                                Svars (kg): 
                                <input v-model="addExerciseModal.weight">
                            </label>
                            <label>
                                Reizes: 
                                <input type="number" v-model.number="addExerciseModal.reps">
                            </label>
                            <button type="button" class="btn btn-secondary" @click="cancelAddingExercise">Atcelt</button>
                            <button type="button" class="btn btn-primary" @click="finishAddingExercise">Pievienot</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `
}).$mount('#workout-creation');