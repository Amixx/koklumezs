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
        <td>{{ tempExercise.exerciseSet.sequenceNo }}</td>
        <td>{{ tempExercise.exercise.name }}</td>
        <td>{{ tempExercise.exerciseSet.reps }}</td>
        <td>
            <div class="form-group">
                <input class="form-control" v-model="tempExercise.weight" style="width:60px;">
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




class Repository {
    static get baseUrl(){
        throw new Error("baseUrl getter must be defined!");
    }
    static get postConfig(){
        return {
            headers: {
                'X-CSRF-Token': getCsrfToken()
            }
        }
    }
}

class ExerciseRepository extends Repository{
    static get baseUrl(){
        return window.getUrl(`/${'fitness-exercises'}`)
    }
    static async list(){
        const data = (await axios.get(`${this.baseUrl}/api-list`)).data
        return data.map(exercise => {
            exercise.sets = exercise.sets.map((set, i) => ({
                ...set,
                sequenceNo: i+1
            }))
            return exercise
        })
    }
}


class WorkoutRepository extends Repository{
    static get baseUrl(){
        return window.getUrl(`/${'fitness-workouts'}`)
    }
    static async ofUser(userId){
        return (await axios.get(`${this.baseUrl}/api-of-student?id=${userId}`)).data
    }

    static async create(workout){
        await axios.post(`${this.baseUrl}/api-create`, workout, this.postConfig)
    }
}


class TemplateRepository extends Repository{
    static get baseUrl(){
        return window.getUrl(`/${'fitness-templates'}`)
    }
    static async list(){
        return (await axios.get(`${this.baseUrl}/api-list`)).data
    }

    static async get(templateId){
        return (await axios.get(`${this.baseUrl}/api-get`, { params: { id: templateId }})).data
    }

    static async create(template){
        return axios.post(`${this.baseUrl}/create`, template, this.postConfig)
    }

    static async update(templateId, template){
        return axios.patch(`${this.baseUrl}/update?id=${templateId}`, template, this.postConfig)
    }
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
                    workout: {
                        studentId: null,
                        workoutExerciseSets: [],
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
                async loadExercises(){
                    this.exercises = await ExerciseRepository.list()
                },
                async loadTemplates(){
                    const templates = (await TemplateRepository.list()).map(template => ({
                        ...template,
                        templateExerciseSets: template.templateExerciseSets.map(tempEx => {
                            let setSequenceNo = 1
                            template.templateExerciseSets.forEach(tempExSet => {
                                if(
                                    tempExSet.exerciseSet.exercise_id === tempEx.exerciseSet.exercise_id &&
                                    parseInt(tempExSet.exerciseSet.id) < parseInt(tempEx.exerciseSet.id))
                                {
                                    setSequenceNo++
                                }
                            })
                            return {
                                id: tempEx.id,
                                exerciseSet: {
                                    ...tempEx.exerciseSet,
                                    sequenceNo: setSequenceNo,
                                },
                                exercise: tempEx.exerciseSet.exercise,
                                weight: tempEx.weight ? parseFloat(tempEx.weight) : null,
                            }
                        })
                    }))

                    this.templates = templates
                },
                loadUser(){
                    axios.get(window.getUrl('/user/api-get?id=' + window.studentId)).then(res => {
                        this.user = res.data
                    })
                },
                async loadUserWorkouts(){
                    this.userWorkouts = await WorkoutRepository.ofUser(window.studentId)
                },
                addExercise(exercise){
                    const exerciseSet = exercise.sets.find((set) => {
                        return !this.workout.workoutExerciseSets.find((workoutExerciseSet) => workoutExerciseSet.exerciseSet.id === set.id)
                    })
                    this.workout.workoutExerciseSets.push({
                        exerciseSet,
                        exercise,
                        weight: null,
                    })
                },
                removeExercise(index){
                    this.workout.workoutExerciseSets.splice(index, 1);
                },
                addTemplate(template){
                    this.workout.workoutExerciseSets.push(...template.templateExerciseSets.map(x => ({...x})));
                },
                async submitWorkout(){
                    await WorkoutRepository.create(this.workout)
                    window.location.replace(getUrl('/assign'));
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
                                            <span style="margin-right: 8px;">{{ template.title }} ({{ template.templateExerciseSets.length }})</span>
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

                            <table class="table table-striped table-bordered" v-if="workout.workoutExerciseSets.length">
                                <thead>
                                    <tr>
                                        <th>Piegājiens</th>
                                        <th>Vingr. pieg.</th>
                                        <th>Vingrinājums</th>
                                        <th>Reizes</th>
                                        <th>Svars</th>
                                        <th>Dzēst</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <added-exercise 
                                        v-for="(exerciseSet, i) in workout.workoutExerciseSets"
                                        :key="i"
                                        :temp-exercise="exerciseSet"
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
                    <div class="col-sm-12 text-center" v-if="workout.workoutExerciseSets.length">
                        <button class="btn btn-primary btn-lg" @click="submitWorkout">Nosūtīt treniņu</button>
                    </div>
                </div>
            </div>
            `
        }).$mount('#' + workoutCreationId);
    }

    if($templateCreation) {
        new Vue({
            data(){
                return {
                    exercises: null,
                    templateId: null,
                    template: {
                        title: null,
                        description: null,
                        templateExerciseSets: [],
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
                async loadExercises(){
                    this.exercises = await ExerciseRepository.list()
                },
                async loadTemplate(){
                    const template = await TemplateRepository.get(window.templateId)                    

                    this.template.title = template.title
                    this.template.description = template.description
                    this.template.templateExerciseSets = template.templateExerciseSets.map(tempEx => {
                        let setSequenceNo = 1
                        template.templateExerciseSets.forEach(tempExSet => {
                            if(
                                tempExSet.exerciseSet.exercise_id === tempEx.exerciseSet.exercise_id &&
                                parseInt(tempExSet.exerciseSet.id) < parseInt(tempEx.exerciseSet.id))
                            {
                                setSequenceNo++
                            }
                        })
                        return {
                            id: tempEx.id,
                            exerciseSet: {
                                ...tempEx.exerciseSet,
                                sequenceNo: setSequenceNo,
                            },
                            exercise: tempEx.exerciseSet.exercise,
                            weight: tempEx.weight ? parseFloat(tempEx.weight) : null,
                        }
                    })
                },
                addExercise(exercise){
                    const exerciseSet = exercise.sets.find((set) => {
                        return !this.template.templateExerciseSets.find((workoutExerciseSet) => workoutExerciseSet.exerciseSet.id === set.id)
                    })
                    this.template.templateExerciseSets.push({
                        exerciseSet,
                        exercise,
                        weight: null,
                    })
                },
                removeExercise(index){
                    this.template.templateExerciseSets.splice(index, 1);
                },
                async createOrUpdateTemplate(){
                    if(this.templateId) {
                        await TemplateRepository.update(this.templateId, this.template)
                        window.location.reload()
                    } else {
                        await TemplateRepository.create(this.template)
                        window.location.replace(window.getUrl('/fitness-templates/index'))
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

                    <div v-if="template.templateExerciseSets.length" class="col-md-6 limit-height">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Piegājiens</th>
                                    <th>Vingr. pieg.</th>
                                    <th>Vingrinājums</th>
                                    <th>Reizes</th>
                                    <th>Svars</th>
                                    <th>Dzēst</th>
                                </tr>
                            </thead>
                            <tbody>
                                <added-exercise 
                                    v-for="(exerciseSet, i) in template.templateExerciseSets"
                                    :key="i"
                                    :temp-exercise="exerciseSet"
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
            </div>
            `
        }).$mount('#' + templateCreationId);
    }
})