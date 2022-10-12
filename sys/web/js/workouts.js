Vue.component('v-select', VueSelect.VueSelect)

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
        },
        showAddSetButton: {
            type: Boolean,
            required: true,
        },
        highlightWeightMissing: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    template: `
    <tr>
        <td>{{ index+1 }}
            <button
                v-if="showAddSetButton"
                class="btn btn-primary"
                @click="$emit('add-set')">
                <span class="glyphicon glyphicon-plus" title="Pievienot nākamo piegājienu"></span>
            </button>
        </td>
        <td>{{ tempExercise.exerciseSet.sequenceNo }}</td>
        <td>{{ tempExercise.exercise.name }}</td>
        <td>{{ tempExercise.exerciseSet.reps }}</td>
        <td>{{ tempExercise.exerciseSet.time_seconds }}</td>
        <td>
            <div class="form-group" :class="{'has-error': highlightWeightMissing && !tempExercise.weight}">
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
    methods: {
        shouldShowEvaluation(userId) {
            return parseInt(userId) === window.studentId;
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
                    <td>
                        <div v-for="(exerciseSet, i) in workout.workoutExerciseSets" :key="i">
                            <div v-for="(eval, j) in exerciseSet.evaluations" :key="j">
                                {{ i+1 }}. vingrojums: 
                                <span v-if="shouldShowEvaluation(eval['user_id'])">
                                    {{ eval['evaluation'] }}
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    `
})


Vue.component('loading-button', {
    name: 'loading-button',
    props: {
        loading: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    template: `
    <button
        class="btn btn-primary btn-lg"
        style='display:flex; gap:8px; margin:auto;'
        :disabled="loading">
        <span><slot></slot></span>
        <div
            class='loader'
            :style="{'visibility': loading ? 'visible' : 'hidden'}"
        ></div>
    </button>
    `
})


Vue.component('success-flash', {
    name: 'success-flash',
    props: {},
    template: `
    <div class="alert-success alert fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <slot></slot>
    </div>
    `
})


Vue.component('error-flash', {
    name: 'error-flash',
    props: {},
    template: `
    <div class="alert-danger alert fade in">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <slot></slot>
    </div>
    `
})


const getCsrfToken = () => {
    return document.querySelector("meta[name=csrf-token]").content
}


class Repository {
    static get baseUrl() {
        throw new Error("baseUrl getter must be defined!");
    }

    static get postConfig() {
        return {
            headers: {
                'X-CSRF-Token': getCsrfToken()
            }
        }
    }
}

class ExerciseRepository extends Repository {
    static get baseUrl() {
        return window.getUrl(`/${'fitness-exercises'}`)
    }

    static async list(tagIdGroups, tagTypes, exerciseName) {
        const data = (await axios.get(`${this.baseUrl}/api-list`, {params: {tagIdGroups, tagTypes, exerciseName}})).data
        return data.map(exercise => {
            exercise.sets = exercise.sets.map((set, i) => ({
                ...set,
                sequenceNo: i + 1
            }))
            return exercise
        })
    }
}

class TagRepository extends Repository {
    static get baseUrl() {
        return window.getUrl(`/${'fitness-tags'}`)
    }

    static async list() {
        const data = (await axios.get(`${this.baseUrl}/api-list`)).data
        return data
    }

    static async listTypeSelectOptions() {
        const data = (await axios.get(`${this.baseUrl}/api-list-type-select-options`)).data
        return data
    }
}


class WorkoutRepository extends Repository {
    static get baseUrl() {
        return window.getUrl(`/${'fitness-workouts'}`)
    }

    static async ofUser(userId) {
        return (await axios.get(`${this.baseUrl}/api-of-student?id=${userId}`)).data
    }

    static async create(workout) {
        await axios.post(`${this.baseUrl}/api-create`, workout, this.postConfig)
    }
}


class TemplateRepository extends Repository {
    static get baseUrl() {
        return window.getUrl(`/${'fitness-templates'}`)
    }

    static async list() {
        return (await axios.get(`${this.baseUrl}/api-list`)).data
    }

    static async get(templateId) {
        return (await axios.get(`${this.baseUrl}/api-get`, {params: {id: templateId}})).data
    }

    static async create(template) {
        return axios.post(`${this.baseUrl}/create`, template, this.postConfig)
    }

    static async update(templateId, template) {
        return axios.patch(`${this.baseUrl}/update?id=${templateId}`, template, this.postConfig)
    }
}


function calcTagBalanceScore(workoutExerciseSets) {
    const score = {}
    workoutExerciseSets.forEach((x) => {
        if (x.exercise.exerciseTags) {
            x.exercise.exerciseTags.forEach((y) => {
                if (!(score.hasOwnProperty(y.tag.value))) {
                    score[y.tag.value] = 0
                }
                score[y.tag.value] += 1
            })
        }
    })
    return score
}


function calcPrevWorkoutTagBalanceScore(workoutExerciseSets) {
    const score = {}
    workoutExerciseSets.forEach((x) => {
        if (x.exerciseSet.exercise) {
            x.exerciseSet.exercise.exerciseTags.forEach((y) => {
                if (!(score.hasOwnProperty(y.tag.value))) {
                    score[y.tag.value] = 0
                }
                score[y.tag.value] += 1
            })
        }
    })
    return score
}


$(document).ready(function () {
    var workoutCreationId = "workout-creation";
    var $workoutCreation = document.getElementById(workoutCreationId);

    var templateCreationId = "template-creation";
    var $templateCreation = document.getElementById(templateCreationId);


    if ($workoutCreation) {
        new Vue({
            data: function () {
                return {
                    exercises: null,
                    exercisesLoading: false,
                    templates: null,
                    user: null,
                    userWorkouts: null,
                    workout: {
                        studentId: null,
                        workoutExerciseSets: [],
                        description: null,
                    },
                    workoutSubmitting: false,
                    highlightWeightMissing: false,
                    tags: null,
                    tagTypeSelectOptions: null,
                    selectedTagTypes: [],
                    selectedTagGroups: [[], [], [], [], []],
                    exerciseNameFilter: '',
                }
            },
            computed: {
                selectedTagGroupsFlat() {
                    return this.selectedTagGroups.flat();
                },
                thisWorkoutTagBalanceScore() {
                    return calcTagBalanceScore(this.workout.workoutExerciseSets)
                },
                prevWorkoutTagBalanceScores() {
                    if (!this.userWorkouts) return {}
                    const scores = {}
                    this.userWorkouts.forEach((x) => {
                        scores[x.created_at] = calcPrevWorkoutTagBalanceScore(x.workoutExerciseSets)
                    })
                    return scores
                },
                prevWorkoutTotalBalanceScore() {
                    const score = {}
                    for (key in this.prevWorkoutTagBalanceScores) {
                        for (key2 in this.prevWorkoutTagBalanceScores[key]) {
                            if (!score.hasOwnProperty(key2)) {
                                score[key2] = 0
                            }
                            score[key2] += this.prevWorkoutTagBalanceScores[key][key2]
                        }
                    }
                    return score
                }
            },
            created() {
                this.loadTemplates();
                this.loadUser();
                this.loadUserWorkouts();
                this.loadTags();
                this.loadTagTypeSelectOptions()
                this.workout.studentId = window.studentId;
            },
            watch: {
                selectedTagGroupsFlat() {
                    this.loadExercises()
                },
                selectedTagTypes() {
                    this.loadExercises()
                }
            },
            methods: {
                async loadTags() {
                    this.tags = await TagRepository.list()
                },
                async loadTagTypeSelectOptions() {
                    const opts = await TagRepository.listTypeSelectOptions()
                    this.tagTypeSelectOptions = Object.keys(opts).map(key => ({
                        value: key,
                        label: opts[key],
                    }))
                },
                async loadExercises() {
                    this.exercisesLoading = true
                    this.exercises = await ExerciseRepository.list(
                        this.selectedTagGroupsFlat.length ? this.selectedTagGroups.map(x => x.map(y => y.id)) : null,
                        this.selectedTagTypes.length ? this.selectedTagTypes.map(x => x.value) : null,
                        this.exerciseNameFilter,
                    )
                    this.exercisesLoading = false
                },
                async loadTemplates() {
                    const templates = (await TemplateRepository.list()).map(template => ({
                        ...template,
                        templateExerciseSets: template.templateExerciseSets.map(tempEx => {
                            let setSequenceNo = 1
                            template.templateExerciseSets.forEach(tempExSet => {
                                if (
                                    tempExSet.exerciseSet.exercise_id === tempEx.exerciseSet.exercise_id &&
                                    parseInt(tempExSet.exerciseSet.id) < parseInt(tempEx.exerciseSet.id)) {
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
                loadUser() {
                    axios.get(window.getUrl('/user/api-get?id=' + window.studentId)).then(res => {
                        this.user = res.data
                    })
                },
                async loadUserWorkouts() {
                    this.userWorkouts = await WorkoutRepository.ofUser(window.studentId)
                },
                addedExercisesOfSet(exercise) {
                    return this.workout.workoutExerciseSets.filter(x => x.exercise.id === exercise.id)
                },
                addExercise(exercise) {
                    const addedSetsOfExercise = this.addedExercisesOfSet(exercise)
                    const exerciseSet = exercise.sets.find((set) => {
                        return !addedSetsOfExercise.find(x => x.exerciseSet.id === set.id)
                    })

                    if (exerciseSet) {
                        this.workout.workoutExerciseSets.push({
                            exerciseSet,
                            exercise,
                            weight: null,
                        })
                    }
                },
                removeExercise(index) {
                    this.workout.workoutExerciseSets.splice(index, 1);
                },
                addTemplate(template) {
                    this.workout.workoutExerciseSets.push(...template.templateExerciseSets.map(x => ({...x})));
                },
                tryAddingAnotherLap() {
                    const exercisesToAdd = []
                    for (let i = this.workout.workoutExerciseSets.length - 1; i >= 0; i--) {
                        const item = this.workout.workoutExerciseSets[i];
                        if (
                            exercisesToAdd.length > 0
                            && exercisesToAdd[exercisesToAdd.length - 1].id === item.exercise.id
                        ) {
                            break;
                        } else {
                            exercisesToAdd.unshift(item.exercise)
                        }
                    }

                    exercisesToAdd.forEach(this.addExercise)
                },
                async submitWorkout() {
                    this.workoutSubmitting = true
                    this.highlightWeightMissing = false
                    try {
                        await WorkoutRepository.create(this.workout)
                        window.location.replace(getUrl('/assign'));
                    } catch (e) {
                        if (e.response?.status === 422) {
                            this.highlightWeightMissing = true
                            this.workoutSubmitting = false
                        }
                    }
                },
                getTagTypeLabel(tagTypeValue){
                    if(!this.tagTypeSelectOptions) return ''
                    const tag = this.tagTypeSelectOptions.find(x => x.value === tagTypeValue)
                    return tag?.label ?? ''
                }
            },
            template: `
            <div>
                 <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                    <li class="nav-item active">
                        <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab" aria-controls="student" aria-selected="false">
                            Klients
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
                        <div class="col-md-6">
                            <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link" id="exercises-tab" data-toggle="tab" href="#exercises" role="tab" aria-controls="exercises" aria-selected="false">
                                        Vingrojumi
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
                                    <ul v-if="tags" class="list-group" style="position:relative">
                                        <li v-show="exercisesLoading" class="list-group-item disabled-overlay">
                                            <div class="loader" style="height:80px;width:80px;margin:auto;margin-top:25%;border-color:green;border-width:8px;border-top-color:gainsboro"></div>
                                        </li>
                                        <li class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <h4>Tagu atlase</h4>
                                            <ul style="padding-left:0;">
                                                <li class="list-group-item" style="border-top:0; border-bottom:0; text-align:center;" v-for="(selectedTags, i) in selectedTagGroups" :key="i">
                                                    <v-select
                                                        label="value"
                                                        :options="tags"
                                                        multiple
                                                        v-model="selectedTagGroups[i]"
                                                    ></v-select>
                                                    <div v-if="i !== selectedTagGroups.length-1">
                                                        VAI
                                                    </div>
                                                </li>
                                            </ul>
                                        </li>
                                         <li v-if="tagTypeSelectOptions" class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                             <h4>Tagu tipu atlase</h4>
                                             <v-select
                                                label="label"
                                                :options="tagTypeSelectOptions"
                                                multiple
                                                v-model="selectedTagTypes"
                                             ></v-select>
                                        </li>
                                        <li class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            Vingrojuma nosaukums:
                                            <div style="display:flex; gap:8px;">
                                                <input type="text" class="form-control" v-model="exerciseNameFilter">
                                                <button class="btn btn-primary" type="button" @click="loadExercises">Ielādēt vingrojumus</button>
                                            </div>
                                        </li>
                                        <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item" style="display:flex; justify-content:space-between; flex-wrap: wrap; gap: 8px;" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <span>
                                                <span style="margin-right: 8px;">
                                                    {{ exercise.name }}
                                                    ({{ addedExercisesOfSet(exercise).length }}/{{ exercise.sets.length }})
                                                </span>
                                                <button
                                                    v-if="exercise.sets.length !== addedExercisesOfSet(exercise).length"
                                                    class="btn btn-primary"
                                                    @click="addExercise(exercise)">
                                                    <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                                </button>
                                            </span>   
                                          
                                            <span class="exercise-tags-container">
                                                <span v-for="exTag in exercise.exerciseTags" class="exercise-tag">
                                                    <span>{{ exTag.tag.value }}</span>
                                                    <span v-if="exTag.tag && exTag.tag.type"> ({{ getTagTypeLabel(exTag.tag.type) }})</span>
                                                </span>
                                            </span>
                                        </li>
                                        <li class="list-group-item" v-if="exercises && exercises.length === 20">
                                            Ielādēti pirmie 20 vingrinājumi, kas atbilst atlasei.
                                        </li>
                                        <li class="list-group-item" v-else-if="exercises && !exercises.length">
                                            Nav atrasts neviens vingrinājums ar šādu tagu atlasi! Mainiet izvēlētos tagus!
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

                        <div class="col-md-6">
                            <h4>Tagu "balance score"</h4>
                            <p>(<em>šajā treniņā</em> | <em>šajā + visos iepriekšējos treniņos</em>)</p>
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(score, key) in thisWorkoutTagBalanceScore" :key="key">
                                    {{ key }}: {{ score }} | {{  prevWorkoutTotalBalanceScore[key] ? score + prevWorkoutTotalBalanceScore[key] : score }}
                                </li>
                                <li class="list-group-item" v-if="!workout.workoutExerciseSets.length">Vēl nav pievienots neviens vingrinājums...</li>
                            </ul>

                            <label class="form-group">
                                Apraksts:
                                <input class="form-control" v-model="workout.description">
                            </label>

                            <error-flash v-if="highlightWeightMissing">Lai izveidotu treniņu, visiem vingrojumiem obligāti jānorāda svars!</error-flash>

                            <div v-if="workout.workoutExerciseSets.length">
                                <table class="table table-striped table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>Piegājiens</th>
                                            <th>Vingr. pieg.</th>
                                            <th>Vingrinājums</th>
                                            <th>Reizes</th>
                                            <th>Laiks (sekundēs)</th>
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
                                            :show-add-set-button="exerciseSet.exercise.sets.length !== addedExercisesOfSet(exerciseSet.exercise).length"
                                            :highlight-weight-missing="highlightWeightMissing"
                                            @add-set="addExercise(exerciseSet.exercise)"
                                            @remove="removeExercise(i)"
                                        ></added-exercise>
                                    </tbody>
                                </table>
                                <div style="text-align:center;">
                                    <button class="btn btn-success" @click="tryAddingAnotherLap">Pievienot nākamo "apli"</button>
                                    <p>Ņem vērā: ja kādam no vingrojumiem, kuru vajadzētu pievienot, nebūs nākamā piegājiena, tas vienkārši tiks izlaists.</p>
                                </div>
                            </div>
                            <p v-else>Treniņam vēl nav pievienots neviens vingrinājums...</p>

                            <h4>Iepriekšējo treniņu tagu "balance score"</h4>
                            <ul class="list-group">
                                <li class="list-group-item" style="margin-bottom: 8px;">
                                    <strong>Iepriekšējo treniņu kopējais "balance score"</strong>
                                    <ul class="list-group">
                                        <li class="list-group-item" v-for="(score, key) in prevWorkoutTotalBalanceScore" :key="key">
                                            {{ key }}: {{ score }}
                                        </li>
                                    </ul>
                                </li>
                                <li class="list-group-item" v-for="(scores, createdAt) in prevWorkoutTagBalanceScores" :key="createdAt">
                                    Treniņš, kurš izveidots {{ createdAt }}:
                                    <ul class="list-group">
                                        <li class="list-group-item" v-for="(score, key) in scores" :key="key">
                                            {{ key }}: {{ score }}
                                        </li>
                                        <li class="list-group-item" v-if="Object.entries(scores).length === 0">Šajā treniņā nav neviena vingrinājuma ar tagiem</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            
                <div class="row" style="margin-top: 16px;">
                    <div class="col-sm-12 text-center" v-if="workout.workoutExerciseSets.length">
                        <loading-button :loading="workoutSubmitting" @click.native="submitWorkout">
                            Nosūtīt treniņu
                        </loading-button>
                    </div>
                </div>
            </div>
            `
        }).$mount('#' + workoutCreationId);
    }

    if ($templateCreation) {
        new Vue({
            data() {
                return {
                    exercises: null,
                    templateId: null,
                    template: {
                        title: null,
                        description: null,
                        templateExerciseSets: [],
                    },
                }
            },
            computed: {
                submitButtonText() {
                    return this.templateId ? 'Saglabāt izmaiņas' : 'Izveidot šablonu';
                }
            },
            created() {
                this.loadExercises();
                if (window.templateId) {
                    this.templateId = window.templateId;
                    this.loadTemplate();
                }
            },
            methods: {
                async loadExercises() {
                    this.exercises = await ExerciseRepository.list(this.selectedTagGroups.map(x => x.map(y => y.id)))
                },
                async loadTemplate() {
                    const template = await TemplateRepository.get(window.templateId)

                    this.template.title = template.title
                    this.template.description = template.description
                    this.template.templateExerciseSets = template.templateExerciseSets.map(tempEx => {
                        let setSequenceNo = 1
                        template.templateExerciseSets.forEach(tempExSet => {
                            if (
                                tempExSet.exerciseSet.exercise_id === tempEx.exerciseSet.exercise_id &&
                                parseInt(tempExSet.exerciseSet.id) < parseInt(tempEx.exerciseSet.id)) {
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
                addedExercisesOfSet(exercise) {
                    return this.template.templateExerciseSets.filter(x => x.exercise.id === exercise.id)
                },
                addExercise(exercise) {
                    const addedSetsOfExercise = this.addedExercisesOfSet(exercise)
                    const exerciseSet = exercise.sets.find((set) => {
                        return !addedSetsOfExercise.find(x => x.exerciseSet.id === set.id)
                    })

                    if (exerciseSet) {
                        this.template.templateExerciseSets.push({
                            exerciseSet,
                            exercise,
                            weight: null,
                        })
                    }
                },
                removeExercise(index) {
                    this.template.templateExerciseSets.splice(index, 1);
                },
                async createOrUpdateTemplate() {
                    if (this.templateId) {
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
                    <div class="col-md-6">
                        <ul v-if="exercises" class="list-group">
                            <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item">
                                <span style="margin-right: 8px;">
                                    {{ exercise.name }}
                                    ({{ addedExercisesOfSet(exercise).length }}/{{ exercise.sets.length }})
                                </span>
                                <button
                                    v-if="exercise.sets.length !== addedExercisesOfSet(exercise).length"
                                    class="btn btn-primary"
                                    @click="addExercise(exercise)">
                                    <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
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
                                    <th>Laiks (sekundēs)</th>
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
                                    :show-add-set-button="exerciseSet.exercise.sets.length !== addedExercisesOfSet(exerciseSet.exercise).length"
                                    @add-set="addExercise(exerciseSet.exercise)"
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