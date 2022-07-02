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
    <div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h3 class="modal-title" id="modal-title" v-if="title">{{ title }}</h3>
                </div>
                <div class="modal-body">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
    `
})


Vue.component('added-exercise', {
    name: 'added-exercise',
    props: {
        tempExercise: {
            type: Object,
            required: true,
        },
        index: {
            type: Number,
            required: true,
        }
    },
    template: `
    <tr>
        <td>{{ index+1 }}</td>
        <td>{{ tempExercise.exercise.name }}</td>
        <td>
            <div class="form-group">
                <input class="form-control" v-model="tempExercise.weight" style="width:60px;">
            </div>
        </td>
        <td>
            <div class="form-group">
                <input type="number" class="form-control" v-model.number="tempExercise.reps" style="width:60px;">
            </div>
        </td>
        <td>
            <button class="btn btn-danger" @click="$emit('remove')">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
    </tr>
    `
})

Vue.component('last-workouts-table', {
    name: 'last-workouts-table',
    props: {
        workouts: {
            type: Array,
            required: true,
        }
    },
    template: `
     <div class="TableContainer" style="max-height: 243px; overflow-y:auto">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Izveidošanas datums</th>
                    <th scope="col">Apraksts</th>
                    <th scope="col">Atvēršanas datums</th>
                    <th scope="col">Novērtējums</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="workout in workouts">
                    <td class="text-center" style="white-space:nowrap">{{ workout.created_at }}</td>
                    <td>{{ workout.description }}</td>
                    <td class="text-center" style="white-space:nowrap">{{ workout.upened_at ? workout.upened_at : 'Nav atvērts' }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    `
})



const getCsrfToken = () => {
    return document.querySelector("meta[name=csrf-token]").content
}


$(document).ready(function(){
    var workoutCreationId = "workout-creation";
    var $workoutCreation = document.getElementById(workoutCreationId);

    var templateCreationId = "template-creation";
    var $templateCreation = document.getElementById(templateCreationId);


    if($workoutCreation) {
        new Vue({
            data: function(){
                return {
                    exercises: null,
                    templates: null,
                    user: null,
                    userWorkouts: null,
                    // exerciseToAdd: null,
                    // addExerciseModal: {
                    //     weight: null,
                    //     reps: null,
                    // },
                    workout: {
                        studentId: null,
                        workoutExercises: [],
                        description: null,
                    }
                }
            },
            created(){
                this.loadExercises();
                this.loadTemplates();
                this.loadUser();
                this.loadUserWorkouts();
                this.workout.studentId = window.studentId;
            },
            methods: {
                loadExercises(){
                    axios.get(window.getUrl('/fitness-exercises/api-list')).then(res => {
                        this.exercises = res.data
                    })
                },
                loadTemplates(){
                    axios.get(window.getUrl('/fitness-templates/api-list')).then(res => {
                        this.templates = res.data
                    })
                },
                loadUser(){
                    axios.get(window.getUrl('/user/api-get?id=' + window.studentId)).then(res => {
                        this.user = res.data
                    })
                },
                loadUserWorkouts(){
                    axios.get(window.getUrl('/fitness-workouts/api-of-student?id=' + window.studentId)).then(res => {
                        this.userWorkouts = res.data
                    })
                },
                // initAddToWorkout(exercise){
                //     this.exerciseToAdd = exercise
                //     this.$nextTick(() => {
                //         $('#add-exercise-modal').modal('show')
                //     })
                // },
                // cancelAddingExercise(){
                //     this.closeAddExerciseModal();
                // },      
                addExercise(exercise){
                    this.workout.workoutExercises.push({
                        exercise,
                        weight: null,
                        reps: null,
                    })
                    // this.cancelAddingExercise()
                },
                removeExercise(index){
                    this.workout.workoutExercises.splice(index, 1);
                },
                addTemplate(template){
                    this.workout.workoutExercises.push(...template.templateExercises.map(x => ({...x})));
                },
                // closeAddExerciseModal(){
                //     $('#add-exercise-modal').modal('hide');
                //     this.exerciseToAdd = null;
                //     this.addExerciseModal = {
                //         weight: null,
                //         reps: null,
                //     }
                // },
                submitWorkout(){
                    axios.post(window.getUrl('/fitness-workouts/api-create'), this.workout, {
                        headers: {
                            'X-CSRF-Token': getCsrfToken()
                        }
                    }).then(() => {
                        window.location.replace(getUrl('/assign'));
                    })
                }
            },
            template: `
            <div>
                 <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab" aria-controls="student" aria-selected="false">
                            Skolēns
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="previous-workouts-tab" data-toggle="tab" href="#previous-workouts" role="tab" aria-controls="previous-workouts" aria-selected="false">
                            Iepriekšējie treniņi
                        </a>
                    </li>
                     <li class="nav-item">
                        <a class="nav-link" id="workout-creation-tab" data-toggle="tab" href="#workout-creation" role="tab" aria-controls="workout-creation" aria-selected="false">
                            Treniņa izveidošana
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                     <div class="row tab-pane fade active in" id="student" role="tabpanel" aria-labelledby="student-tab">
                        <div class="col-md-12" v-if="user">
                            <p>Vārds: {{ user.first_name }} {{ user.last_name }}</p>
                            <p>E-pasts: {{ user.email }}</p>
                            <p>Valoda: {{ user.language }}</p>
                        </div>
                    </div>

                    <div class="row tab-pane fade" id="previous-workouts" role="tabpanel" aria-labelledby="previous-workouts-tab">
                        <div class="col-md-12">
                            <last-workouts-table v-if="userWorkouts" :workouts="userWorkouts"></last-workouts-table>
                        </div>
                    </div>

                    <div class="row tab-pane fade" id="workout-creation" role="tabpanel" aria-labelledby="workout-creation-tab">
                        <div class="col-md-6 limit-height">
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
                                            <span style="margin-right: 8px;">{{ exercise.name }}</span>
                                            <button class="btn btn-primary" @click="addExercise(exercise)">
                                                <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates-tab">
                                    <ul v-if="templates" class="list-group">
                                        <li v-for="template in templates" :key="template.id" class="list-group-item">
                                            <span style="margin-right: 8px;">{{ template.title }} ({{ template.templateExercises.length }})</span>
                                            <button class="btn btn-primary" @click="addTemplate(template)">
                                                <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 limit-height">
                            <label class="form-group">
                                Apraksts:
                                <input class="form-control" v-model="workout.description">
                            </label>

                            <table class="table table-striped table-bordered" v-if="workout.workoutExercises.length">
                                <thead>
                                    <tr>
                                        <th>Nr.</th>
                                        <th>Vingrinājums</th>
                                        <th>Svars</th>
                                        <th>Reizes</th>
                                        <th>Dzēsts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <added-exercise 
                                        v-for="(exercise, i) in workout.workoutExercises"
                                        :key="i"
                                        :temp-exercise="exercise"
                                        :index="i"
                                        @remove="removeExercise(i)"
                                    ></added-exercise>
                                </tbody>
                            </table>
                            <p v-else>Treniņam vēl nav pievienots neviens vingrinājums...</p>
                        </div>
                    </div>
                </div>
            
                <div class="row" style="margin-top: 16px;">
                    <div class="col-sm-12 text-center" v-if="workout.workoutExercises.length">
                        <button class="btn btn-primary btn-lg" @click="submitWorkout">Nosūtīt treniņu</button>
                    </div>
                </div>

                <!-- <modal v-if="exerciseToAdd" id="add-exercise-modal" :title="'Pievienot vingrinājumu ' + exerciseToAdd.name">
                    <form>
                        <label class="form-group">
                            Svars (kg): 
                            <input v-model="addExerciseModal.weight" class="form-control">
                        </label>
                        <label class="form-group">
                            Reizes: 
                            <input type="number" v-model.number="addExerciseModal.reps" class="form-control">
                        </label>
                        <button type="button" class="btn btn-secondary" @click="cancelAddingExercise">Atcelt</button>
                        <button type="button" class="btn btn-primary" @click="finishAddingExercise">Pievienot</button>
                    </form>
                </modal> -->
            </div>
            `
        }).$mount('#' + workoutCreationId);
    }

    if($templateCreation) {
        new Vue({
            data(){
                return {
                    exercises: null,
                    // exerciseToAdd: null,
                    // addExerciseModal: {
                    //     weight: null,
                    //     reps: null,
                    // },
                    templateId: null,
                    template: {
                        title: null,
                        description: null,
                        tempExercises: [],
                    }
                }
            },
            computed: {
                submitButtonText(){
                    return this.templateId ? 'Saglabāt izmaiņas' : 'Izveidot šablonu';
                }
            },
            created(){
                this.loadExercises();
                if(window.templateId) {
                    this.templateId = window.templateId;
                    this.loadTemplate();
                }
            },
            methods: {
                loadExercises(){
                    axios.get(window.getUrl('/fitness-exercises/api-list')).then(res => {
                        this.exercises = res.data
                    })
                },
                loadTemplate(){
                    axios.get(window.getUrl('/fitness-templates/api-get'), { params: { id: window.templateId }}).then(res => {
                        this.template.title = res.data.title;
                        this.template.description = res.data.description;
                        this.template.tempExercises = res.data.templateExercises.map(tempEx => ({
                            ...tempEx,
                            weight: tempEx.weight ? parseFloat(tempEx.weight) : null,
                            reps: tempEx.reps ? parseInt(tempEx.reps) : null,
                        }))
                    })
                },
                // initAddToWorkout(exercise){
                //     this.exerciseToAdd = exercise
                //     this.$nextTick(() => {
                //         $('#add-exercise-modal').modal('show')
                //     })
                // },
                // cancelAddingExercise(){
                //     this.closeAddExerciseModal();
                // },      
                addExercise(exercise){
                    this.template.tempExercises.push({
                        exercise: exercise,
                        weight: null,
                        reps: null,
                    })
                    // this.cancelAddingExercise()
                },
                removeExercise(index){
                    this.template.tempExercises.splice(index, 1);
                },
                // closeAddExerciseModal(){
                //     $('#add-exercise-modal').modal('hide');
                //     this.exerciseToAdd = null;
                //     this.addExerciseModal = {
                //         weight: null,
                //         reps: null,
                //     }
                // },
                createOrUpdateTemplate(){
                    if(this.templateId) {
                        axios.patch(window.getUrl('/fitness-templates/update?id=' + this.templateId), this.template, {
                            headers: {
                                'X-CSRF-Token': getCsrfToken()
                            }
                        })
                    } else {
                        axios.post(window.getUrl('/fitness-templates/create'), this.template, {
                            headers: {
                                'X-CSRF-Token': getCsrfToken()
                            }
                        }).then(() => {
                            window.location.replace(getUrl('/fitness-templates/index'));
                        })
                    }
                },
            },
            template: `
            <div>
                <div class="row">
                    <form class="col-sm-12">
                        <label class="form-group">
                            Nosaukums:
                            <input v-model="template.title" class="form-control">
                        </label>
                        <label class="form-group">
                            Apraksts:
                            <input v-model="template.description" class="form-control">
                        </label>
                    </form>
                </div>

                <div class="row">
                    <div class="col-md-6 limit-height">
                    <ul v-if="exercises" class="list-group">
                            <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item">
                                <span>{{ exercise.name }}</span>
                                <button class="btn btn-primary" @click="addExercise(exercise)">
                                    <span class="glyphicon glyphicon-plus" title="Pievienot šablonam"></span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div v-if="template.tempExercises.length" class="col-md-6 limit-height">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nr.</th>
                                    <th>Vingrinājums</th>
                                    <th>Svars</th>
                                    <th>Reizes</th>
                                    <th>Dzēst</th>
                                </tr>
                            </thead>
                            <tbody>
                                <added-exercise 
                                    v-for="(exercise, i) in template.tempExercises"
                                    :key="i"
                                    :temp-exercise="exercise"
                                    :index="i"
                                    @remove="removeExercise(i)"
                                ></added-exercise>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary btn-lg" @click="createOrUpdateTemplate">{{ submitButtonText }}</button>
                    </div>
                </div>

                <!-- <modal v-if="exerciseToAdd" id="add-exercise-modal" :title="'Pievienot vingrinājumu ' + exerciseToAdd.name">
                    <form>
                        <label class="form-group">
                            Svars (kg): 
                            <input v-model="addExerciseModal.weight" class="form-control">
                        </label>
                        <label class="form-group">
                            Reizes: 
                            <input type="number" v-model.number="addExerciseModal.reps" class="form-control">
                        </label>
                        <button type="button" class="btn btn-secondary" @click="cancelAddingExercise">Atcelt</button>
                        <button type="button" class="btn btn-primary" @click="finishAddingExercise">Pievienot</button>
                    </form>
                </modal> -->
            </div>
            `
        }).$mount('#' + templateCreationId);
    }
})