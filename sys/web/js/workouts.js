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
    created() {
        this.$nextTick(() => {
            $(this.$refs.modal).modal('show');
        })
    },
    beforeDestroy() {
        $(this.$refs.modal).modal('hide');
    },
    methods: {
        close() {
            this.$emit('close')
        }
    },
    template: `
    <div class="modal fade" :id="id" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true" ref="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
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
    },
    data() {
        if (this.tempExercise.exercise.is_pause === '1') {
            const pauseLengths = [
                15,
                30,
                45,
                60,
                90,
                120,
                150,
                180,
                240,
                300,
                360,
            ];
            const pauseLengthOptions = pauseLengths.map(seconds => ({value: seconds, label: seconds}));
            const selectedPauseLength = this.tempExercise.time_seconds
                ? pauseLengthOptions.find(x => x.value === this.tempExercise.time_seconds)
                : pauseLengthOptions[1];

            return {
                pauseLengthOptions: pauseLengthOptions,
                selectedPauseLength: selectedPauseLength,
            };
        }

        return {
            weightPercentageOf1rm: null,
            oneRepMaxEstimate: null,
        }
    },
    computed: {
        specialVideoShownMessage() {
            if (!this.tempExercise.exercise.videos) return null;

            var resForReps = false;
            var resForTime = false;
            var resForBoth = false;
            var specialVideoToShow = this.tempExercise.exercise.videos.find(v => {
                var repsMatch = v.reps && this.tempExercise.reps && this.tempExercise.reps == v.reps;
                var timeMatches = v.time_seconds && this.tempExercise.time_seconds && this.tempExercise.time_seconds == v.time_seconds;

                var forReps = v.reps && !v.time_seconds && repsMatch;
                var forTime = v.time_seconds && !v.reps && timeMatches;
                var forBoth = v.reps && v.time_seconds && repsMatch && timeMatches;

                if (forReps || forTime || forBoth) {
                    resForReps = forReps;
                    resForTime = forTime;
                    resForBoth = forBoth;
                    return true;
                }
            });

            if (specialVideoToShow) {
                if (resForReps) {
                    return 'Tiks rādīts īpašais video <strong>' + this.tempExercise.reps + ' reizēm</strong>';
                }
                if (resForTime) {
                    return 'Tiks rādīts īpašais video <strong>' + this.tempExercise.time_seconds + ' sekundēm</strong>';
                }
                if (resForBoth) {
                    return 'Tiks rādīts īpašais video <strong>' + this.tempExercise.reps + ' reizēm</strong> un <strong>' + this.tempExercise.time_seconds + ' sekundēm</strong>';
                }
            }

            return null;
        },
        rpe() {
            if (
                !this.tempExercise.reps
                || this.tempExercise < 1
                || this.tempExercise.reps > 12
                || !this.weightPercentageOf1rm) return null;
            return RpeCalculator.calculateRpe(this.tempExercise.reps, this.weightPercentageOf1rm);
        },
    },
    watch: {
        selectedPauseLength: {
            handler(n) {
                if (n) this.tempExercise.time_seconds = n.value;
            },
            immediate: true
        },
        'tempExercise.weight': {
            handler() {
                if (!this.oneRepMaxEstimate) return;
                if (this.ignoreWeightWatcher) {
                    this.ignoreWeightWatcher = false;
                    return;
                } else {
                    this.ignoreWeightPercentageOf1rmWatcher = true
                    this.setWeightPercentageOf1rm();
                }
            }
        },
        weightPercentageOf1rm() {
            if (!this.oneRepMaxEstimate) return;
            if (this.ignoreWeightPercentageOf1rmWatcher) {
                this.ignoreWeightPercentageOf1rmWatcher = false;
                return;
            } else {
                this.ignoreWeightWatcher = true;
                this.setWeight();
            }
        }
    },
    async mounted() {
        const averageAbility = await ExerciseRepository.getAverageAbility(this.tempExercise.exercise.id);
        // TODO: implement functionality for bodyweight max reps
        if (averageAbility.ability && averageAbility.type === '1rm') {
            this.oneRepMaxEstimate = averageAbility.ability;
        }
    },
    methods: {
        setWeightPercentageOf1rm() {
            if (!this.tempExercise.weight) {
                this.weightPercentageOf1rm = null;
                return;
            }
            this.weightPercentageOf1rm = ((this.tempExercise.weight / this.oneRepMaxEstimate) * 100).toFixed(0) + '%';
        },
        setWeight() {
            if (!this.weightPercentageOf1rm) {
                this.tempExercise.weight = null;
                return;
            }
            this.tempExercise.weight = parseInt(this.oneRepMaxEstimate * parseInt(this.weightPercentageOf1rm) / 100);
        }
    },
    template: `
    <tr>
        <td>{{ index+1 }}
            <button
                class="btn btn-primary"
                @click="$emit('add-set')">
                <span class="glyphicon glyphicon-plus" title="Pievienot nākamo piegājienu"></span>
            </button>
        </td>
        <td>
            <span>{{ tempExercise.sequenceNo }}</span>
            <span 
                v-if="specialVideoShownMessage" 
                class="text-info" 
                style="display:inline-block;font-size:12px;" 
                v-html="specialVideoShownMessage">
            </span>
        </td>
        <td>
            <span>{{ tempExercise.exercise.name }}</span>
        </td>
        <td>
            <div v-if="!(tempExercise.exercise.is_pause === '1')" class="form-group">
                <input class="form-control" v-model.number="tempExercise.reps" style="width:60px;">
            </div>
        </td>
        <td>
            <div class="form-group">
                 <div v-if="!(tempExercise.exercise.is_pause === '1') && tempExercise.exercise.is_bodyweight === '1'" class="form-group">
                    <input class="form-control" v-model.number="tempExercise.time_seconds" style="width:60px;">
                 </div>
                 <v-select
                    v-else-if="tempExercise.exercise.is_pause === '1'"
                    label="label"
                    :options="pauseLengthOptions"
                    v-model="selectedPauseLength"
                 ></v-select>
            </div>
        </td>
         <td>
            <div v-if="!(tempExercise.exercise.is_pause === '1')" class="form-group">
                <input class="form-control" v-model="oneRepMaxEstimate" readonly style="width:70px;">
            </div>
        </td>
        <td>
            <div v-if="!(tempExercise.exercise.is_pause === '1') && tempExercise.exercise.is_bodyweight !== '1'" class="form-group">
                <input class="form-control" v-model="weightPercentageOf1rm" style="width:75px;">
            </div>
        </td>
        <td>
            <div v-if="!(tempExercise.exercise.is_pause === '1') && tempExercise.exercise.is_bodyweight !== '1'" class="form-group">
                <input class="form-control" v-model.number="tempExercise.weight" style="width:50px;">
            </div>
        </td>
         <td>
            <div v-if="!(tempExercise.exercise.is_pause === '1')" class="form-group">
                <input class="form-control" v-model="rpe" readonly style="width:45px;">
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

var evalValueToText = {
    2: 'Garlaicīgi',
    4: "Viegli",
    6: 'Nedaudz grūti',
    8: 'Ļoti grūti',
    10: 'Neiespējami',
};

function formatAbilityRange(abilityRange, unit) {
    var min = abilityRange.min;
    var max = abilityRange.max;
    var suffix = " (" + unit + ")";

    var main = min === max
        ? min
        : [min, max].filter(x => x !== null).join("-");

    return main + suffix;
}

Vue.component('last-workouts-table', {
    name: 'last-workouts-table',
    props: {
        workouts: {
            type: Array,
            required: true,
        }
    },
    methods: {
        getFileExtension(fileString) {
            return fileString.split('.').pop();
        },
        formatAbilitiesRange(workoutExercise) {
            if (!workoutExercise.evaluation) return "";

            if (workoutExercise.evaluation.one_rep_max_range) {
                return formatAbilityRange(workoutExercise.evaluation.one_rep_max_range, "kg");
            }
            if (workoutExercise.evaluation.max_reps_range) {
                return formatAbilityRange(workoutExercise.evaluation.max_reps_range, "r");
            }
            if (workoutExercise.evaluation.max_time_seconds_range) {
                return formatAbilityRange(workoutExercise.evaluation.max_time_seconds_range, "s");
            }

            return "";
        },
        exerciseNameWithFallback(exerciseName) {
            return exerciseName ? "<strong>" + exerciseName.name + "</strong>" : '(<em>dzēsts vingrojums<em>)';
        },
        formatWorkoutExerciseName(workoutExercise) {
            if (!workoutExercise.replacementExercise) {
                return this.exerciseNameWithFallback(workoutExercise.exercise)
            }

            return this.exerciseNameWithFallback(workoutExercise.replacementExercise.exercise) + " (aizstāja oriģinālo vingrojumu " + this.exerciseNameWithFallback(workoutExercise.exercise) + ")";
        },
        getAttribute(workoutExercise, attribute) {
            return workoutExercise.replacementExercise
                ? workoutExercise.replacementExercise[attribute]
                : workoutExercise[attribute]
        }
    },
    template: `
     <div class="TableContainer">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Izveidošanas datums</th>
                    <th scope="col">Apraksts</th>
                    <th scope="col">Kad atvērts</th>
                    <th scope="col">Vingrojumi un novērtējumi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="workout in workouts">
                    <td class="text-center" style="white-space:nowrap">{{ workout.created_at }}</td>
                    <td>{{ workout.description }}</td>
                    <td class="text-center" style="white-space:nowrap">{{ workout.opened_at ? workout.opened_at : 'Nav atvērts' }}</td>
                    <td>
                        <span v-if="workout.abandoned === '1'" class="text-danger" style="line-height: 2.5; font-weight: bold;">Šis treniņš tika pamests!</span>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nr.</th>
                                    <th>Vingrojums</th>
                                    <th>Reizes</th>
                                    <th>Laiks (sekundēs)</th>
                                    <th>Svars (kg)</th>
                                    <th>Novērtējums</th>
                                    <th>Spējas (1RM / max reizes / max laiks)</th>
                                </tr>
                            </thead>
                            <tbody>
                                 <tr v-for="(workoutExercise, i) in workout.workoutExercises" :key="i">
                                    <td>{{ i+1 }}</td>
                                    <td v-html="formatWorkoutExerciseName(workoutExercise)"></td>
                                    <td>{{ getAttribute(workoutExercise, 'reps') }}</td>
                                    <td>{{ getAttribute(workoutExercise, 'time_seconds') }}</td>
                                    <td>{{ getAttribute(workoutExercise, 'weight') }}</td>
                                    <td>{{ workoutExercise.evaluation ? workoutExercise.evaluation.evaluation_text : "" }}</td>
                                    <td>{{ formatAbilitiesRange(workoutExercise) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="workout.abandoned === '0'">
                            <h4>Treniņa novērtējums: <span v-if="workout.evaluation">{{ evalValueToText[workout.evaluation.evaluation] }}</span></h4>
                            
                            <h4>Ziņa trenerim:</h4>
                            <div v-if="workout.messageForCoach">
                                <div style="display:flex; flex-wrap:wrap; gap: 16px;">
                                     <div v-if="workout.messageForCoach.video" style="max-width: 300px">
                                        <video id="post-workout-message-video" playsinline controls data-role="player" style="width:100%">
                                            <source :src="'/sys/files/' + workout.messageForCoach.video" :type="'video/' + getFileExtension(workout.messageForCoach.video)"/>
                                        </video>
                                    </div>
                                    <div>
                                        <p v-if="workout.messageForCoach.text">{{ workout.messageForCoach.text }}</p>
                                        <div v-if="workout.messageForCoach.audio" style="max-width: 300px">
                                            <audio id="post-workout-message-video" controls data-role="player" style="width:100%">
                                                <source :src="'/sys/files/' + workout.messageForCoach.audio" :type="'audio/' + getFileExtension(workout.messageForCoach.video)"/>
                                            </audio>
                                        </div>
                                    </div>
                                </div>
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
        class="btn btn-primary"
        style='display:flex; gap:8px; margin:auto;justify-content:center;'
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

Vue.component('exercise-creation-modal', {
    name: 'exercise-creation-modal',
    props: {
        initialName: {
            type: String,
            required: false,
            default: ''
        }
    },
    data() {
        return {
            name: '',
            description: '',
            isLoading: false,
        }
    },
    created() {
        this.name = this.initialName;
    },
    methods: {
        async submit() {
            this.isLoading = true;
            try {
                return await ExerciseRepository.create({name: this.name, description: this.description});
            } catch (e) {
                console.log(e);
            } finally {
                this.isLoading = false;
            }
        },
        async submitWithoutAdding() {
            this.submit();
            this.$emit('close');
        },
        async submitAndAddToWorkout() {
            var exercise = await this.submit();
            this.$emit('add-to-workout', exercise);
            this.$emit('close');
        }
    },
    template: `
    <modal id="exercise-creation-modal" title="Vingrojuma izveidošana" @close="$emit('close')">
        <div>
            <div class="form-group required">
                <label class="control-label" for="exercise-name">Nosaukums</label>
                <input
                    v-model="name"
                    type="text"
                    id="exercise-name"
                    class="form-control"
                    name="Exercise[name]"
                    aria-required="true">
            </div>
            <div class="form-group field-exercise-description">
                <label class="control-label" for="exercise-description">Apraksts</label>
                <textarea
                    v-model="description"
                    id="exercise-description"
                    class="form-control"
                    name="Exercise[description]"
                ></textarea>
            </div>
        </div>
        <div style="display:flex; gap: 8px; justify-content: center;">
            <button type="button" class="btn" @click="$emit('close')">Atcelt</button>        
            <button class="btn btn-primary" :disabled="!name.length || isLoading" @click="submitWithoutAdding">Izveidot</button>        
            <button class="btn btn btn-success" :disabled="!name.length || isLoading" @click="submitAndAddToWorkout">Izveidot un piešķirt treniņam</button>        
        </div>
    </modal>      
    `
})

const rpeArray = [
    100, 97.8, 95.5, 93.9, 92.2, 90.7, 89.2, 87.8,
    86.3, 85.0, 83.7, 82.4, 81.1, 79.9, 78.6, 77.4,
    76.2, 75.1, 73.9, 72.3, 70.7, 69.4, 68.0, 66.7,
    65.3, 64.0, 62.6, 61.3, 59.9, 58.6
];
const rpes = [10, 9.5, 9, 8.5, 8, 7.5, 7, 6.5];

const rpeTable = {}

for (let rep = 1; rep <= 12; rep++) {
    const rpeArrayOffset = (rep - 1) * 2;
    rpes.forEach(function (rpe, i) {
        if (!rpeTable[rep]) rpeTable[rep] = {}
        rpeTable[rep][rpe] = rpeArray[i + rpeArrayOffset];
    })
}

class RpeCalculator {
    static calculateRpe(reps, weightPercentageOf1rm) {
        var x = rpeTable[reps]
        var smallestDiff = null
        var toReturn = null
        for (const y in x) {
            var newDiff = Math.abs(weightPercentageOf1rm - x[y])
            if (!smallestDiff || newDiff < smallestDiff) {
                smallestDiff = newDiff
                toReturn = y
            }
        }

        return toReturn
    }
}


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

    static async list(tagIdGroups, tagTypes, exerciseName, exercisePopularity) {
        return (await axios.get(`${this.baseUrl}/api-list`, {
            params: {
                tagIdGroups,
                tagTypes,
                exerciseName,
                exercisePopularity
            }
        })).data
    }

    static async listPauses() {
        const data = (await axios.get(`${this.baseUrl}/api-list`, {params: {onlyPauses: true}})).data;
        return data.map(exercise => {
            exercise.sets = exercise.sets.map((set, i) => ({
                ...set,
                sequenceNo: i + 1
            }))
            return exercise
        })
    }

    static async create(exercise) {
        return (await axios.post(`${this.baseUrl}/api-create`, exercise, this.postConfig)).data
    }

    static async getAverageAbility(id) {
        return (await axios.get(`${this.baseUrl}/api-get-average-ability?id=${id}&userId=${window.studentId}`)).data
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


// function calcTagBalanceScore(workoutExercises) {
//     const score = {}
//     workoutExercises.forEach((x) => {
//         if (x.exercise.exerciseTags) {
//             x.exercise.exerciseTags.forEach((y) => {
//                 if (!(score.hasOwnProperty(y.tag.value))) {
//                     score[y.tag.value] = 0
//                 }
//                 score[y.tag.value] += 1
//             })
//         }
//     })
//     return score
// }

//
// function calcPrevWorkoutTagBalanceScore(workoutExercises) {
//     const score = {}
//     workoutExercises.forEach((x) => {
//         if (x.exercise) {
//             x.exercise.exerciseTags.forEach((y) => {
//                 if (!(score.hasOwnProperty(y.tag.value))) {
//                     score[y.tag.value] = 0
//                 }
//                 score[y.tag.value] += 1
//             })
//         }
//     })
//     return score
// }


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
                    pauses: null,
                    exercisesLoading: false,
                    templates: null,
                    user: null,
                    userWorkouts: null,
                    workout: {
                        studentId: null,
                        workoutExercises: [],
                        description: null,
                    },
                    workoutSubmitting: false,
                    tags: null,
                    tagTypeSelectOptions: null,
                    selectedTagTypes: [],
                    selectedTagGroups: [[], [], [], [], []],
                    exerciseNameFilter: '',
                    exercisePopularitySelectOptions: [
                        {
                            value: 'POPULAR',
                            label: 'Populārs'
                        },
                        {
                            value: 'AVERAGE',
                            label: 'Vidēji populārs'
                        },
                        {
                            value: 'RARE',
                            label: 'Rets'
                        },
                    ],
                    selectedExercisePopularity: null,
                    showCreateExerciseModal: false,
                    creatingExercise: false,
                }
            },
            computed: {
                selectedTagGroupsFlat() {
                    return this.selectedTagGroups.flat();
                },
                // thisWorkoutTagBalanceScore() {
                //     return calcTagBalanceScore(this.workout.workoutExercises)
                // },
                // prevWorkoutTagBalanceScores() {
                //     if (!this.userWorkouts) return {}
                //     const scores = {}
                //     this.userWorkouts.forEach((x) => {
                //         scores[x.created_at] = calcPrevWorkoutTagBalanceScore(x.workoutExercises)
                //     })
                //     return scores
                // },
                // prevWorkoutTotalBalanceScore() {
                //     const score = {}
                //     for (key in this.prevWorkoutTagBalanceScores) {
                //         for (key2 in this.prevWorkoutTagBalanceScores[key]) {
                //             if (!score.hasOwnProperty(key2)) {
                //                 score[key2] = 0
                //             }
                //             score[key2] += this.prevWorkoutTagBalanceScores[key][key2]
                //         }
                //     }
                //     return score
                // }
            },
            created() {
                this.loadTemplates();
                this.loadUser();
                this.loadUserWorkouts();
                this.loadTags();
                this.loadTagTypeSelectOptions()
                this.loadPauses()
                this.workout.studentId = window.studentId;
            },
            watch: {
                selectedTagGroupsFlat(n, o) {
                    if (!o || n.length > o.length) {
                        this.loadExercises()
                    }
                },
                selectedTagTypes() {
                    this.loadExercises()
                },
                selectedExercisePopularity() {
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
                    const tagIdGroups = this.selectedTagGroupsFlat.length ? this.selectedTagGroups.map(x => x.map(y => y.id)) : null
                    const tagTypes = this.selectedTagTypes.length ? this.selectedTagTypes.map(x => x.value) : null
                    const exercisePopularity = this.selectedExercisePopularity ? this.selectedExercisePopularity.value : null
                    this.exercises = await ExerciseRepository.list(tagIdGroups, tagTypes, this.exerciseNameFilter, exercisePopularity)
                    this.exercisesLoading = false
                },
                async loadPauses() {
                    this.pauses = await ExerciseRepository.listPauses()
                },
                async loadTemplates() {
                    const templates = (await TemplateRepository.list()).map(template => ({
                        ...template,
                        templateExercises: template.templateExercises.map(tempEx => {
                            return {
                                id: tempEx.id,
                                exercise: tempEx.exercise,
                                weight: tempEx.weight ? parseFloat(tempEx.weight) : null,
                                reps: tempEx.reps ? parseFloat(tempEx.reps) : null,
                                time_seconds: tempEx.time_seconds ? parseFloat(tempEx.time_seconds) : null,
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
                    return this.workout.workoutExercises.filter(x => x.exercise.id === exercise.id)
                },
                addExercise(exercise) {
                    const setsOfExercise = this.workout.workoutExercises.filter(we => we.exercise.id === exercise.id)
                    const lastSetOfExercise = setsOfExercise.length ? setsOfExercise.pop() : null

                    const newWorkoutExercise = {
                        exercise,
                        sequenceNo: this.addedExercisesOfSet(exercise).length + 1,
                        reps: null,
                        time_seconds: null,
                        weight: null,
                    }
                    if (lastSetOfExercise) {
                        newWorkoutExercise.reps = lastSetOfExercise.reps
                        newWorkoutExercise.time_seconds = lastSetOfExercise.time_seconds
                        newWorkoutExercise.weight = lastSetOfExercise.weight
                    }

                    this.workout.workoutExercises.push(newWorkoutExercise)
                },
                removeExercise(index) {
                    const removed = this.workout.workoutExercises.splice(index, 1)
                    this.lowerAddedExerciseSetNumbers(removed[0])
                },
                lowerAddedExerciseSetNumbers(removedExercise) {
                    this.workout.workoutExercises.forEach(x => {
                        if (removedExercise.exercise.id === x.exercise.id) x.sequenceNo--
                    })
                },
                addTemplate(template) {
                    this.workout.workoutExercises.push(...template.templateExercises.map(x => ({
                        ...x,
                        sequenceNo: this.addedExercisesOfSet(x.exercise).length + 1
                    })));
                },
                addAnotherLap() {
                    const exercisesToAdd = []
                    for (let i = this.workout.workoutExercises.length - 1; i >= 0; i--) {
                        const item = this.workout.workoutExercises[i];
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
                    try {
                        await WorkoutRepository.create(this.workout)
                        window.location.replace(getUrl('/assign'));
                    } catch (e) {
                        if (e.response?.status === 422) {
                            console.error(e.response)
                            this.workoutSubmitting = false
                        }
                    }
                },
                async createAndAddSearchValueExercise() {
                    this.creatingExercise = true
                    const exercise = await ExerciseRepository.create({name: this.exerciseNameFilter});
                    this.creatingExercise = false

                    this.addJustCreated(exercise)
                },
                async addJustCreated(exercise) {
                    this.addExercise(exercise)
                    if (this.exerciseNameFilter.length >= 3) this.loadExercises()
                }
            },
            template: `
            <div class="workout-creation-container">
                <div class="row">
                    <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab" aria-controls="student" aria-selected="false">
                                Klients
                            </a>
                        </li>
                        <li class="nav-item active">
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
                </div>

                <div class="row tab-content">
                     <div class="tab-pane fade" id="student" role="tabpanel" aria-labelledby="student-tab">
                        <div class="col-md-12" v-if="user">
                            <p>Vārds: {{ user.first_name }} {{ user.last_name }}</p>
                            <p>E-pasts: {{ user.email }}</p>
                            <p>Tel. nr.: {{ user.phone_number }}</p>
                            <p>Valoda: {{ user.language }}</p>
                            <div v-if="user.about">
                                <p>Piezīmes:</p>
                                <p v-html="user.about"></p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade active in" id="previous-workouts" role="tabpanel" aria-labelledby="previous-workouts-tab">
                        <div class="col-md-12">
                            <last-workouts-table v-if="userWorkouts" :workouts="userWorkouts"></last-workouts-table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="workout-creation" role="tabpanel" aria-labelledby="workout-creation-tab">
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
                                     <h4>Vingrojumu meklēšana</h4>
                                     <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                                        <li class="nav-item active">
                                            <a class="nav-link" id="exercises-tab" data-toggle="tab" href="#by-name" role="tab" aria-controls="exercises" aria-selected="true">
                                                Pēc nosaukuma
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="templates-tab" data-toggle="tab" href="#by-tags" role="tab" aria-controls="templates" aria-selected="false">
                                                Pēc tagiem un popularitātes
                                            </a>
                                        </li>
                                    </ul>
                                    
                                   
                                
                                    <ul v-if="tags" class="list-group" style="position:relative">
                                         <div class="tab-content" id="by-name-tab-content">
                                            <div class="tab-pane fade active in" id="by-name" role="tabpanel" aria-labelledby="exercises-tab">
                                                <div class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                                    <div class="text-right">
                                                        <button
                                                            class="btn btn-default"
                                                            style="margin-bottom:8px;"
                                                            @click="showCreateExerciseModal = true">
                                                            Izveidot vingrojumu
                                                        </button>
                                                    </div>
                                                    <div style="display:flex; gap:8px;">
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            v-model="exerciseNameFilter"
                                                            @keydown.enter="() => {
                                                                if(!(exerciseNameFilter.length < 3)) loadExercises()
                                                            }">
                                                        <button
                                                            class="btn btn-primary"
                                                            type="button"
                                                            :disabled="exerciseNameFilter.length < 3"
                                                            :title="exerciseNameFilter.length < 3 ? 'Ievadiet vismaz 3 simbolus!' : ''"
                                                            @click="loadExercises">
                                                            Meklēt
                                                        </button>
                                                    </div>                                                   
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="by-tags" role="tabpanel" aria-labelledby="exercises-tab">
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
                                                     <h4>Vingrojumu popularitātes atlase</h4>
                                                     <v-select
                                                        label="label"
                                                        :options="exercisePopularitySelectOptions"
                                                        v-model="selectedExercisePopularity"
                                                     ></v-select>
                                                </li>
                                            </div>
                                        </div>
                                    
                                        <li v-show="exercisesLoading" class="list-group-item disabled-overlay">
                                            <div class="loader" style="height:80px;width:80px;margin:auto;margin-top:25%;border-color:green;border-width:8px;border-top-color:gainsboro"></div>
                                        </li>                                       
                                        <li v-if="exercises" class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <p style="text-align: center;font-size: 18px;margin-bottom: 0;">Vingrojumi</p>
                                        </li>
                                        <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item" style="display:flex; justify-content:space-between; flex-wrap: wrap; gap: 8px;" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <span>
                                                <a :href="'/sys/fitness-exercises/view?id=' + exercise.id" title="Apskatīt vingrojumu" target="_blank">
                                                    <span class="glyphicon glyphicon-eye-open"></span>
                                                </a>
                                                <span style="margin-right: 8px;">{{ exercise.name }}</span>
                                                <button
                                                    class="btn btn-primary"
                                                    @click="addExercise(exercise)">
                                                    <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                                </button>
                                            </span>   
                                        </li>
                                        <li class="list-group-item" v-if="exercises && exercises.length === 20" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            Ielādēti pirmie 20 vingrojumi, kas atbilst atlasei.
                                        </li>
                                        <li class="list-group-item" v-else-if="exercises && !exercises.length" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <span>Nav atrasts neviens vingrojums!</span>
                                            <button
                                                v-if="exerciseNameFilter"
                                                class="btn btn-success"
                                                :disabled="creatingExercise"
                                                @click="createAndAddSearchValueExercise">
                                                Izveidot un piešķirt <strong>{{ exerciseNameFilter }}</strong>
                                            </button>
                                        </li>
                                         <li class="list-group-item" v-if="pauses" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                            <p style="text-align: center;font-size: 18px;margin-bottom: 0;">Pauzes</p>
                                            <p v-for="(pause, i) in pauses" :key="i">
                                                <a :href="'/sys/fitness-exercises/view?id=' + pause.id" title="Apskatīt pauzi" target="_blank">
                                                    <span class="glyphicon glyphicon-eye-open"></span>
                                                </a>
                                                <span>{{ pause.name }}</span>
                                                <button
                                                    class="btn btn-primary"
                                                    @click="addExercise(pause)">
                                                    <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                                </button>
                                            </p>
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

                        <div class="col-md-6">
<!--                            <h4>Tagu "balance score"</h4>-->
<!--                            <p>(<em>šajā treniņā</em> | <em>šajā + visos iepriekšējos treniņos</em>)</p>-->
<!--                            <ul class="list-group">-->
<!--                                <li class="list-group-item" v-for="(score, key) in thisWorkoutTagBalanceScore" :key="key">-->
<!--                                    {{ key }}: {{ score }} | {{  prevWorkoutTotalBalanceScore[key] ? score + prevWorkoutTotalBalanceScore[key] : score }}-->
<!--                                </li>-->
<!--                                <li class="list-group-item" v-if="!workout.workoutExercises.length">Vēl nav pievienots neviens vingrojums...</li>-->
<!--                            </ul>-->

                            <label class="form-group">
                                Apraksts:
                                <input class="form-control" v-model="workout.description">
                            </label>

                            <div v-if="workout.workoutExercises.length">
                                <table class="table table-striped table-bordered" >
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Vingr. pieg.</th>
                                            <th>Vingrojums</th>
                                            <th>Reizes</th>
                                            <th>Laiks (sekundēs)</th>
                                            <th>Spējas (1RM)</th>
                                            <th>Svars (% no 1RM)</th>
                                            <th>Svars (kg)</th>
                                            <th>RPE</th>
                                            <th>Dzēst</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <added-exercise 
                                            v-for="(workoutExercise, i) in workout.workoutExercises"
                                            :key="i"
                                            :temp-exercise="workoutExercise"
                                            :index="i"
                                            @add-set="addExercise(workoutExercise.exercise)"
                                            @remove="removeExercise(i)"
                                        ></added-exercise>
                                    </tbody>
                                </table>
                                <div style="margin-top: 16px; display: flex;gap:16px;">
                                    <button class="btn btn-default w-100" style="width:100%;" @click="addAnotherLap">
                                        Pievienot nākamo "apli"
                                    </button>
                                    <loading-button :loading="workoutSubmitting" @click.native="submitWorkout" style="width:100%;">
                                        Nosūtīt treniņu
                                    </loading-button>
                                </div>
                              
                            </div>
                            <p v-else>Treniņam vēl nav pievienots neviens vingrojums...</p>

<!--                            <h4>Iepriekšējo treniņu tagu "balance score"</h4>-->
<!--                            <ul class="list-group">-->
<!--                                <li class="list-group-item" style="margin-bottom: 8px;">-->
<!--                                    <strong>Iepriekšējo treniņu kopējais "balance score"</strong>-->
<!--                                    <ul class="list-group">-->
<!--                                        <li class="list-group-item" v-for="(score, key) in prevWorkoutTotalBalanceScore" :key="key">-->
<!--                                            {{ key }}: {{ score }}-->
<!--                                        </li>-->
<!--                                    </ul>-->
<!--                                </li>-->
<!--                                <li class="list-group-item" v-for="(scores, createdAt) in prevWorkoutTagBalanceScores" :key="createdAt">-->
<!--                                    Treniņš, kurš izveidots {{ createdAt }}:-->
<!--                                    <ul class="list-group">-->
<!--                                        <li class="list-group-item" v-for="(score, key) in scores" :key="key">-->
<!--                                            {{ key }}: {{ score }}-->
<!--                                        </li>-->
<!--                                        <li class="list-group-item" v-if="Object.entries(scores).length === 0">Šajā treniņā nav neviena vingrojuma ar tagiem</li>-->
<!--                                    </ul>-->
<!--                                </li>-->
<!--                            </ul>-->
                        </div>
                    </div>
                </div>
                
                <exercise-creation-modal
                    v-if="showCreateExerciseModal"
                    :initial-name="exerciseNameFilter"
                    @close="showCreateExerciseModal = false"
                    @add-to-workout="addJustCreated"/>
            </div>
            `
        }).$mount('#' + workoutCreationId);
    }

    if ($templateCreation) {
        new Vue({
            data() {
                return {
                    exercises: null,
                    pauses: null,
                    exercisesLoading: false,
                    workoutSubmitting: false,
                    tags: null,
                    tagTypeSelectOptions: null,
                    selectedTagTypes: [],
                    selectedTagGroups: [[], [], [], [], []],
                    exerciseNameFilter: '',
                    exercisePopularitySelectOptions: [
                        {
                            value: 'POPULAR',
                            label: 'Populārs'
                        },
                        {
                            value: 'AVERAGE',
                            label: 'Vidēji populārs'
                        },
                        {
                            value: 'RARE',
                            label: 'Rets'
                        },
                    ],
                    selectedExercisePopularity: null,
                    templateId: null,
                    template: {
                        title: null,
                        templateExercises: [],
                        description: null,
                    },
                }
            },
            computed: {
                selectedTagGroupsFlat() {
                    return this.selectedTagGroups.flat();
                },
                submitButtonText() {
                    return this.templateId ? 'Saglabāt izmaiņas' : 'Izveidot šablonu';
                }
            },
            created() {
                this.loadTags();
                this.loadTagTypeSelectOptions()
                this.loadPauses()
                if (window.templateId) {
                    this.templateId = window.templateId;
                    this.loadTemplate();
                }
            },
            watch: {
                selectedTagGroupsFlat(n, o) {
                    if (!o || n.length > o.length) {
                        this.loadExercises()
                    }
                },
                selectedTagTypes() {
                    this.loadExercises()
                },
                selectedExercisePopularity() {
                    this.loadExercises()
                }
            },
            methods: {
                async loadExercises() {
                    this.exercisesLoading = true
                    const tagIdGroups = this.selectedTagGroupsFlat.length ? this.selectedTagGroups.map(x => x.map(y => y.id)) : null
                    const tagTypes = this.selectedTagTypes.length ? this.selectedTagTypes.map(x => x.value) : null
                    const exercisePopularity = this.selectedExercisePopularity ? this.selectedExercisePopularity.value : null
                    this.exercises = await ExerciseRepository.list(tagIdGroups, tagTypes, this.exerciseNameFilter, exercisePopularity)
                    this.exercisesLoading = false
                },
                async loadPauses() {
                    this.pauses = await ExerciseRepository.listPauses()
                },
                async loadTemplate() {
                    const template = await TemplateRepository.get(window.templateId)

                    this.template.title = template.title
                    this.template.description = template.description
                    this.template.templateExercises = template.templateExercises.map(tempEx => ({
                        id: tempEx.id,
                        exercise: tempEx.exercise,
                        weight: tempEx.weight ? parseFloat(tempEx.weight) : null,
                        reps: tempEx.reps ? parseFloat(tempEx.reps) : null,
                        time_seconds: tempEx.time_seconds ? parseFloat(tempEx.time_seconds) : null,
                    }))
                },
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
                addedExercisesOfSet(exercise) {
                    return this.template.templateExercises.filter(x => x.exercise.id === exercise.id)
                },
                addExercise(exercise) {
                    const setsOfExercise = this.template.templateExercises.filter(we => we.exercise.id === exercise.id)
                    const lastSetOfExercise = setsOfExercise.length ? setsOfExercise.pop() : null

                    const newWorkoutExercise = {
                        exercise,
                        sequenceNo: this.addedExercisesOfSet(exercise).length + 1,
                        reps: null,
                        time_seconds: null,
                        weight: null,
                    }
                    if (lastSetOfExercise) {
                        newWorkoutExercise.reps = lastSetOfExercise.reps
                        newWorkoutExercise.time_seconds = lastSetOfExercise.time_seconds
                        newWorkoutExercise.weight = lastSetOfExercise.weight
                    }

                    this.template.templateExercises.push(newWorkoutExercise)
                },
                removeExercise(index) {
                    const removed = this.template.templateExercises.splice(index, 1)
                    this.lowerAddedExerciseSetNumbers(removed[0])
                },
                lowerAddedExerciseSetNumbers(removedExercise) {
                    this.template.templateExercises.forEach(x => {
                        if (removedExercise.exercise.id === x.exercise.id) x.sequenceNo--
                    })
                },
                addAnotherLap() {
                    const exercisesToAdd = []
                    for (let i = this.template.templateExercises.length - 1; i >= 0; i--) {
                        const item = this.template.templateExercises[i];
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
                       <h4>Vingrojumu meklēšana</h4>
                       <ul class="nav nav-tabs" id="exercise-tabs" role="tablist">
                         <li class="nav-item active">
                            <a class="nav-link" id="exercises-tab" data-toggle="tab" href="#by-name" role="tab" aria-controls="exercises" aria-selected="true">
                                Pēc nosaukuma
                            </a>
                         </li>
                         <li class="nav-item">
                            <a class="nav-link" id="templates-tab" data-toggle="tab" href="#by-tags" role="tab" aria-controls="templates" aria-selected="false">
                                Pēc tagiem un popularitātes
                            </a>
                         </li>
                       </ul>
                       <ul v-if="tags" class="list-group" style="position:relative">
                             <div class="tab-content" id="by-name-tab-content">
                                <div class="tab-pane fade active in" id="by-name" role="tabpanel" aria-labelledby="exercises-tab">
                                    <div class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                        <div class="text-right">
                                            <button
                                                class="btn btn-default"
                                                style="margin-bottom:8px;"
                                                @click="showCreateExerciseModal = true">
                                                Izveidot vingrojumu
                                            </button>
                                        </div>
                                        <div style="display:flex; gap:8px;">
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="exerciseNameFilter"
                                                @keydown.enter="() => {
                                                    if(!(exerciseNameFilter.length < 3)) loadExercises()
                                                }">
                                            <button
                                                class="btn btn-primary"
                                                type="button"
                                                :disabled="exerciseNameFilter.length < 3"
                                                :title="exerciseNameFilter.length < 3 ? 'Ievadiet vismaz 3 simbolus!' : ''"
                                                @click="loadExercises">
                                                Meklēt
                                            </button>
                                        </div>                                                   
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="by-tags" role="tabpanel" aria-labelledby="exercises-tab">
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
                                         <h4>Vingrojumu popularitātes atlase</h4>
                                         <v-select
                                            label="label"
                                            :options="exercisePopularitySelectOptions"
                                            v-model="selectedExercisePopularity"
                                         ></v-select>
                                    </li>
                                </div>
                            </div>
                        
                            <li v-show="exercisesLoading" class="list-group-item disabled-overlay">
                                <div class="loader" style="height:80px;width:80px;margin:auto;margin-top:25%;border-color:green;border-width:8px;border-top-color:gainsboro"></div>
                            </li>                                       
                            <li v-if="exercises" class="list-group-item" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                <p style="text-align: center;font-size: 18px;margin-bottom: 0;">Vingrojumi</p>
                            </li>
                            <li v-for="exercise in exercises" :key="exercise.id" class="list-group-item" style="display:flex; justify-content:space-between; flex-wrap: wrap; gap: 8px;" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                <span>
                                    <a :href="'/sys/fitness-exercises/view?id=' + exercise.id" title="Apskatīt vingrojumu" target="_blank">
                                        <span class="glyphicon glyphicon-eye-open"></span>
                                    </a>
                                    <span style="margin-right: 8px;">{{ exercise.name }}</span>
                                    <button
                                        class="btn btn-primary"
                                        @click="addExercise(exercise)">
                                        <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                    </button>
                                </span>   
                            </li>
                            <li class="list-group-item" v-if="exercises && exercises.length === 20" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                Ielādēti pirmie 20 vingrojumi, kas atbilst atlasei.
                            </li>
                            <li class="list-group-item" v-else-if="exercises && !exercises.length" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                <span>Nav atrasts neviens vingrojums!</span>
                            </li>
                             <li class="list-group-item" v-if="pauses" :style="{ 'z-index': exercisesLoading ? '-1' : 'auto' }">
                                <p style="text-align: center;font-size: 18px;margin-bottom: 0;">Pauzes</p>
                                <p v-for="(pause, i) in pauses" :key="i">
                                    <a :href="'/sys/fitness-exercises/view?id=' + pause.id" title="Apskatīt pauzi" target="_blank">
                                        <span class="glyphicon glyphicon-eye-open"></span>
                                    </a>
                                    <span>{{ pause.name }}</span>
                                    <button
                                        class="btn btn-primary"
                                        @click="addExercise(pause)">
                                        <span class="glyphicon glyphicon-plus" title="Pievienot treniņam"></span>
                                    </button>
                                </p>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                    <div v-if="template.templateExercises.length">
                            <table class="table table-striped table-bordered" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vingr. pieg.</th>
                                        <th>Vingrojums</th>
                                        <th>Reizes</th>
                                        <th>Laiks (sekundēs)</th>
                                        <th>Spējas (1RM)</th>
                                        <th>Svars (% no 1RM)</th>
                                        <th>Svars (kg)</th>
                                        <th>RPE</th>
                                        <th>Dzēst</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <added-exercise 
                                        v-for="(templateExercise, i) in template.templateExercises"
                                        :key="i"
                                        :temp-exercise="templateExercise"
                                        :index="i"
                                        @add-set="addExercise(templateExercise.exercise)"
                                        @remove="removeExercise(i)"
                                    ></added-exercise>
                                </tbody>
                            </table>
                            <div style="margin-top: 16px; display: flex;gap:16px;">
                                <button class="btn btn-default w-100" style="width:100%;" @click="addAnotherLap">
                                    Pievienot nākamo "apli"
                                </button>
                            </div>
                        </div>
                        <p v-else>Treniņam vēl nav pievienots neviens vingrojums...</p>
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