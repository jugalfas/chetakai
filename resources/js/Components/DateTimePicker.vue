<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { 
  format, 
  addMonths, 
  subMonths, 
  startOfMonth, 
  endOfMonth, 
  startOfWeek, 
  endOfWeek, 
  eachDayOfInterval, 
  isSameMonth, 
  isSameDay, 
  isToday, 
  setHours, 
  setMinutes, 
  getHours, 
  getMinutes,
  isBefore,
  startOfDay
} from 'date-fns'
import { 
  Calendar as CalendarIcon, 
  Clock, 
  ChevronLeft, 
  ChevronRight,
  ChevronDown
} from 'lucide-vue-next'
import {
  PopoverRoot,
  PopoverTrigger,
  PopoverContent,
  PopoverPortal,
  SelectRoot,
  SelectTrigger,
  SelectValue,
  SelectContent,
  SelectViewport,
  SelectItem,
  SelectPortal,
} from 'radix-vue'

interface Props {
  modelValue: Date | null
  minDate?: Date
  maxDate?: Date
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: null,
  disabled: false
})

const emit = defineEmits<{
  (e: 'update:modelValue', value: Date | null): void
}>()

// Internal state
const isOpen = ref(false)
const currentMonth = ref(props.modelValue || new Date())
const selectedDate = ref<Date | null>(props.modelValue)

// Time state
const selectedHour = ref(props.modelValue ? getHours(props.modelValue).toString().padStart(2, '0') : '00')
const selectedMinute = ref(props.modelValue ? (Math.min(Math.floor(getMinutes(props.modelValue) / 5) * 5, 55)).toString().padStart(2, '0') : '00')

// Constants
const hours = Array.from({ length: 24 }, (_, i) => i.toString().padStart(2, '0'))
const minutes = Array.from({ length: 12 }, (_, i) => (i * 5).toString().padStart(2, '0'))
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

// Computed
const formattedDateDisplay = computed(() => {
  if (!props.modelValue) return 'Select date & time'
  return format(props.modelValue, 'MMMM do, yyyy HH:mm')
})

const calendarDays = computed(() => {
  const start = startOfWeek(startOfMonth(currentMonth.value))
  const end = endOfWeek(endOfMonth(currentMonth.value))
  return eachDayOfInterval({ start, end })
})

const isDateDisabled = (date: Date) => {
  if (props.minDate && isBefore(startOfDay(date), startOfDay(props.minDate))) return true
  if (props.maxDate && isBefore(startOfDay(props.maxDate), startOfDay(date))) return true
  return false
}

// Methods
const nextMonth = () => {
  currentMonth.value = addMonths(currentMonth.value, 1)
}

const prevMonth = () => {
  currentMonth.value = subMonths(currentMonth.value, 1)
}

const selectDate = (date: Date) => {
  if (isDateDisabled(date)) return
  selectedDate.value = date
  updateModelValue()
}

const updateModelValue = () => {
  if (!selectedDate.value) return
  
  let newDate = setHours(selectedDate.value, parseInt(selectedHour.value))
  newDate = setMinutes(newDate, parseInt(selectedMinute.value))
  
  emit('update:modelValue', newDate)
}

// Watchers
watch(() => props.modelValue, (newVal) => {
  if (newVal) {
    selectedDate.value = newVal
    selectedHour.value = getHours(newVal).toString().padStart(2, '0')
    selectedMinute.value = (Math.min(Math.floor(getMinutes(newVal) / 5) * 5, 55)).toString().padStart(2, '0')
    if (!isSameMonth(currentMonth.value, newVal)) {
      currentMonth.value = newVal
    }
  } else {
    selectedDate.value = null
  }
})

watch([selectedHour, selectedMinute], () => {
  if (selectedDate.value) {
    updateModelValue()
  }
})

</script>

<template>
  <div class="relative w-full">
    <PopoverRoot v-model:open="isOpen">
      <PopoverTrigger
        class="flex h-12 w-full items-center justify-between rounded-md border border-white/10 bg-white/5 px-4 text-sm font-medium transition-colors hover:bg-white/10 focus:outline-none focus:ring-1 focus:ring-white/20 disabled:cursor-not-allowed disabled:opacity-50 text-white"
        :disabled="disabled"
      >
        <span class="flex items-center gap-3">
          <CalendarIcon class="h-4 w-4 text-white/40" />
          <span :class="!modelValue && 'text-white/40'">{{ formattedDateDisplay }}</span>
        </span>
        <ChevronDown class="h-4 w-4 text-white/40" />
      </PopoverTrigger>

      <PopoverPortal>
        <PopoverContent
          class="z-[100] w-[300px] rounded-xl border border-white/10 bg-[#0B1E34] p-4 text-white shadow-2xl outline-none"
          align="start"
          :side-offset="8"
        >
          <!-- Calendar Header -->
          <div class="mb-4 flex items-center justify-between px-1">
            <span class="text-sm font-bold">
              {{ format(currentMonth, 'MMMM yyyy') }}
            </span>
            <div class="flex gap-1">
              <button
                @click="prevMonth"
                class="rounded-md p-1.5 hover:bg-white/10 transition-colors"
                type="button"
              >
                <ChevronLeft class="h-4 w-4 text-white/60" />
              </button>
              <button
                @click="nextMonth"
                class="rounded-md p-1.5 hover:bg-white/10 transition-colors"
                type="button"
              >
                <ChevronRight class="h-4 w-4 text-white/60" />
              </button>
            </div>
          </div>

          <!-- Calendar Grid -->
          <div class="grid grid-cols-7 gap-1 mb-4">
            <div
              v-for="day in dayNames"
              :key="day"
              class="h-9 w-9 flex items-center justify-center text-[10px] font-bold text-white/40 uppercase"
            >
              {{ day.substring(0, 2) }}
            </div>
            <button
              v-for="date in calendarDays"
              :key="date.toISOString()"
              @click="selectDate(date)"
              type="button"
              :disabled="isDateDisabled(date)"
              :class="[
                'h-9 w-9 flex items-center justify-center rounded-md text-sm transition-all relative',
                !isSameMonth(date, currentMonth) ? 'text-white/20' : 'text-white',
                isSameDay(date, selectedDate || new Date(0)) ? 'bg-[#F59E0B] text-white font-bold shadow-lg shadow-[#F59E0B]/20' : 'hover:bg-white/10',
                isToday(date) && !isSameDay(date, selectedDate || new Date(0)) ? 'text-[#F59E0B] font-bold' : '',
                isDateDisabled(date) ? 'opacity-10 cursor-not-allowed' : 'cursor-pointer'
              ]"
            >
              {{ format(date, 'd') }}
              <div v-if="isToday(date) && !isSameDay(date, selectedDate || new Date(0))" class="absolute bottom-1.5 left-1/2 -translate-x-1/2 w-1 h-1 bg-[#F59E0B] rounded-full"></div>
            </button>
          </div>

          <!-- Time Selection -->
          <div class="pt-4 border-t border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white/60">
              <Clock class="h-4 w-4" />
              <span class="text-xs font-medium">Time</span>
            </div>
            
            <div class="flex items-center gap-2">
              <!-- Hour Select -->
              <SelectRoot v-model="selectedHour">
                <SelectTrigger
                  class="flex h-9 w-[60px] items-center justify-between rounded-md bg-white/5 border border-white/10 px-2 text-xs font-medium hover:bg-white/10 transition-colors outline-none focus:ring-1 focus:ring-white/20 text-white"
                >
                  <SelectValue>{{ selectedHour }}</SelectValue>
                  <ChevronDown class="h-3 w-3 text-white/40" />
                </SelectTrigger>
                <SelectPortal>
                  <SelectContent
                    class="z-[110] min-w-[60px] overflow-hidden rounded-md border border-white/10 bg-[#0B1E34] text-white shadow-2xl"
                    position="popper"
                    :side-offset="5"
                  >
                    <SelectViewport class="p-1 max-h-[200px]">
                      <SelectItem
                        v-for="hour in hours"
                        :key="hour"
                        :value="hour"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-2 px-2 text-xs font-medium outline-none data-[highlighted]:bg-[#F59E0B] data-[highlighted]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                      >
                        {{ hour }}
                      </SelectItem>
                    </SelectViewport>
                  </SelectContent>
                </SelectPortal>
              </SelectRoot>

              <span class="text-white/20 font-bold">:</span>

              <!-- Minute Select -->
              <SelectRoot v-model="selectedMinute">
                <SelectTrigger
                  class="flex h-9 w-[60px] items-center justify-between rounded-md bg-white/5 border border-white/10 px-2 text-xs font-medium hover:bg-white/10 transition-colors outline-none focus:ring-1 focus:ring-white/20 text-white"
                >
                  <SelectValue>{{ selectedMinute }}</SelectValue>
                  <ChevronDown class="h-3 w-3 text-white/40" />
                </SelectTrigger>
                <SelectPortal>
                  <SelectContent
                    class="z-[110] min-w-[60px] overflow-hidden rounded-md border border-white/10 bg-[#0B1E34] text-white shadow-2xl"
                    position="popper"
                    :side-offset="5"
                  >
                    <SelectViewport class="p-1 max-h-[200px]">
                      <SelectItem
                        v-for="minute in minutes"
                        :key="minute"
                        :value="minute"
                        class="relative flex w-full cursor-default select-none items-center rounded-sm py-2 px-2 text-xs font-medium outline-none data-[highlighted]:bg-[#F59E0B] data-[highlighted]:text-white data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                      >
                        {{ minute }}
                      </SelectItem>
                    </SelectViewport>
                  </SelectContent>
                </SelectPortal>
              </SelectRoot>
            </div>
          </div>
        </PopoverContent>
      </PopoverPortal>
    </PopoverRoot>
  </div>
</template>

<style scoped>
/* Optional: Hide scrollbar for SelectViewport */
[data-radix-select-viewport] {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
[data-radix-select-viewport]::-webkit-scrollbar {
  display: none;
}
</style>
