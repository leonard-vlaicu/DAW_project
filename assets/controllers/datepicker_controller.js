import {Controller} from '@hotwired/stimulus';
import flatpickr from 'flatpickr';

export default class extends Controller {
    static values = {
        mode: {type: String, default: 'single'},
        minDate: String,
        maxDate: String,
        dateFormat: {type: String, default: 'Y-m-d'},
        enableTime: {type: Boolean, default: false},
        disable: Array,
        targetBegin: String,
        targetEnd: String,
        bookId: Number,
        unavailableDatesUrl: String
    }

    async connect() {
        let disabledDates = this.hasDisableValue ? this.parseDisabledDates() : [];

        if (this.hasBookIdValue && this.hasUnavailableDatesUrlValue) {
            try {
                const response = await fetch(this.unavailableDatesUrlValue);
                const unavailableDates = await response.json();
                disabledDates = [...disabledDates, ...unavailableDates];
            } catch (error) {
                console.error('Error fetching unavailable dates:', error);
            }
        }

        this.disabledDatesArray = disabledDates;

        const config = {
            mode: this.modeValue,
            dateFormat: this.dateFormatValue,
            enableTime: this.enableTimeValue,
            minDate: this.hasMinDateValue ? this.minDateValue : undefined,
            maxDate: this.hasMaxDateValue ? this.maxDateValue : undefined,
            disable: disabledDates,
            altInput: true,
            altFormat: 'F j, Y',
            onDayCreate: (dObj, dStr, fp, dayElem) => {
                const date = dayElem.dateObj;
                const dateStr = this.formatDate(date);

                if (this.disabledDatesArray.includes(dateStr)) {
                    dayElem.style.color = 'rgba(255,0,0,0.5)';
                    dayElem.style.backgroundColor = 'rgba(255,0,0,0.2)';
                    dayElem.style.fontWeight = 'bold';
                }
            }
        };

        if (this.modeValue === 'range' && this.hasTargetBeginValue && this.hasTargetEndValue) {
            config.onChange = (selectedDates, dateStr, instance) => {

                if (selectedDates.length === 2) {
                    const beginInput = document.querySelector(this.targetBeginValue);
                    const endInput = document.querySelector(this.targetEndValue);

                    if (beginInput && endInput) {
                        const beginDate = this.formatDate(selectedDates[0]);
                        const endDate = this.formatDate(selectedDates[1]);

                        beginInput.value = beginDate;
                        endInput.value = endDate;

                        beginInput.dispatchEvent(new Event('change', {bubbles: true}));
                        endInput.dispatchEvent(new Event('change', {bubbles: true}));
                    }
                } else if (selectedDates.length === 1) {
                    const endInput = document.querySelector(this.targetEndValue);
                    if (endInput) {
                        endInput.value = '';
                    }
                }
            };

            config.onReady = () => {
                const beginInput = document.querySelector(this.targetBeginValue);
                const endInput = document.querySelector(this.targetEndValue);

                if (beginInput && endInput && beginInput.value && endInput.value) {
                    this.picker.setDate([beginInput.value, endInput.value], false);
                }
            };
        }

        this.picker = flatpickr(this.element, config);
    }

    formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    parseDisabledDates() {
        return this.disableValue.map(item => {
            if (typeof item === 'string' && item.includes(' to ')) {
                const [from, to] = item.split(' to ');
                return {from: from.trim(), to: to.trim()};
            }
            return item;
        });
    }

    disconnect() {
        if (this.picker) {
            this.picker.destroy();
        }
    }
}
