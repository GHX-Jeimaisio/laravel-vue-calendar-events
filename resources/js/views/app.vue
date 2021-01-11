<template>
    <div class="m-5 card-body bg-white">
        <h3>Calendar</h3>
        <hr>
        <div class="row">
            <div class="col-md-4">
                <FlashMessage :position="'right top'"></FlashMessage>
                <form @submit.prevent="store()">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="d-block">Event</label>
                            <input required type="text" v-model="data.name" name="event_name"/>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="d-block">From</label>
                            <input required type="date" v-model="data.date_from" name="from"/>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="d-block">To</label>
                            <input required type="date" v-model="data.date_to" name="to"/>
                        </div>
                        <div class="col-md-12 form-group">
                            <div class="form-check d-inline-block mr-1" v-for="d in days">
                                <input type="checkbox" v-model="data.days[d]" class="form-check-input">
                                <label class="form-check-label"> {{d}}</label>
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div class="event-list" v-for="lists in calendar">
                    <div v-for="days in lists" class="position-relative" :class="{ toggle: highlight}">
                        <div v-if="events.length > 0" v-for="event in events">
                            <div class="row clear border-bottom-1">
                                <h3 v-if="days.month">{{days.value}}</h3>
                                <div class="col-md-2 align-middle" v-else>{{days.value}}</div>
                                <div class="col-md-10 align-middle highlight" v-for="event_date in event.has_many_event_schedule"
                                     v-if="event_date.date == days.date">{{event.name}}
                                </div>
                            </div>
                        </div>
                        <div class="row clear border-bottom-1" v-if="events.length < 1">
                            <h3 v-if="days.month">{{days.value}}</h3>
                            <div class="col-md-2 align-middle" v-else>{{days.value}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .border-bottom-1{
        border-bottom: 1px solid black;
        height: 50px;
        align-content: center;
    }
    .align-middle{
        height: 100%;
        display: flex;
        align-content: center;
        align-items: center;
    }
    .highlight{
        background: #2EEE2E;
    }
</style>
<script>
    const default_layout = "default";
    import axios from "axios";
    import moment from "moment"

    export default {
        data: () => {
            return {
                startDate: new Date(),
                endDate: new Date(),
                data: {
                    days: {}
                },
                highlight: false,
                events: [],
                calendar: [],
                days: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                monthNames: ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ]
            }
        },
        async mounted() {
            await this.list();
        },
        methods: {
            async list() {
                axios
                    .get("/event")
                    .then(resp => {
                        this.events = resp.data;

                        if (this.events.length > 0 && this.events[0].has_many_event_schedule.length > 0) {
                            this.startDate = new Date(this.events[0].has_many_event_schedule[0].date);
                            this.endDate = new Date(this.events[0].has_many_event_schedule[this.events[0].has_many_event_schedule.length - 1].date);
                        }

                        this.getDaysArray(this.startDate, this.endDate)
                    })
                    .catch(err => console.log(err.response.data));
            },
            async store(data) {
                axios
                    .post("/event", {
                        ...this.data
                    })
                    .then(data => {
                        this.list(); // update our list of events
                        this.showAlert();
                    })
                    .catch(err =>
                        console.log("Unable to add new event!", err.response.data)
                    );
            },
            async getDaysArray(start, end) {
                var date1 = moment(start);
                var date2 = moment(end);
                var selectedMonth = [];

                for (date1.month(); date1 <= date2; date1.add(1, 'M')) {
                    var result = [];
                    var monthIndex = date1.month(); // 0..11 instead of 1..12
                    var names = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    var date = new Date(date1.year(), monthIndex, 1);
                    result.push({month: true, value: this.monthNames[monthIndex] + ' ' + date1.year()})
                    while (date.getMonth() == monthIndex) {
                        var setMonth = ("0" + (monthIndex + 1)).slice(-2);
                        var setDate = ("0" + date.getDate()).slice(-2);
                        result.push({
                            month: false,
                            value: date.getDate() + ' ' + names[date.getDay()],
                            date: date1.year() + '-' + setMonth + '-' + setDate
                        });
                        date.setDate(date.getDate() + 1);
                    }
                    selectedMonth.push(result)
                }
                this.calendar = selectedMonth;
                console.log(selectedMonth)
            },
            showAlert() {
                this.flashMessage.success({
                    message: 'Event successfully saved.',
                    time: 5000,
                    position: 'right-top',
                    flashMessageStyle: {
                        backgroundColor: 'linear-gradient(#e66465, #9198e5)'
                    }
                });
            }

        }
    };
</script>
