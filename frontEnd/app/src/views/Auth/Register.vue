<template>
  <div class="animate-fadeIn">
    <div class="mb-10 relative">
      <div class="flex justify-between items-center relative z-10">
        <div v-for="step in 4" :key="step" class="flex flex-col items-center">
          <div
            class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm transition-all duration-500 shadow-sm"
            :class="[
              step === currentStep ? 'bg-slate-900 text-white scale-110 shadow-lg shadow-blue-500/20' :
              step < currentStep ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-400'
            ]"
          >
            <svg v-if="step < currentStep" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
            <span v-else>{{ step }}</span>
          </div>

          <span
            class="text-[10px] mt-3 font-bold uppercase tracking-wider transition-colors duration-300"
            :class="step <= currentStep ? 'text-slate-900' : 'text-slate-400'"
          >
            {{ stepLabels[step - 1] }}
          </span>
        </div>
      </div>

      <div class="absolute top-5 left-0 w-full h-[2px] bg-slate-100 -z-0">
        <div
          class="h-full bg-blue-600 transition-all duration-500 ease-out"
          :style="{ width: `${((currentStep - 1) / 3) * 100}%` }"
        ></div>
      </div>
    </div>

    <div class="relative overflow-hidden min-h-[400px]">
      <transition
        name="step-fade"
        mode="out-in"
      >
        <component
          :is="currentStepComponent"
          :key="currentStep"
          @next="nextStep"
          @previous="previousStep"
        />
      </transition>
    </div>

    <div class="mt-8 pt-6 border-t border-slate-50 text-center">
      <p class="text-[11px] text-slate-400 font-medium">
        STEP {{ currentStep }} OF 4 • SECURITY ENCRYPTED
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Step1BasicInfo from '@/components/auth/Step1BasicInfo.vue'
import Step2Media from '@/components/auth/Step2Media.vue'
import Step3Password from '@/components/auth/Step3Password.vue'
import Step4Review from '@/components/auth/Step4Review.vue'

const currentStep = ref(1)
const stepLabels = ['Basic', 'Media', 'Privacy', 'Review']

const currentStepComponent = computed(() => {
  const components = [Step1BasicInfo, Step2Media, Step3Password, Step4Review]
  return components[currentStep.value - 1]
})

const nextStep = () => {
  if (currentStep.value < 4) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 1) {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    currentStep.value--
  }
}
</script>

<style scoped>
.animate-fadeIn {
  animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Step Transition Animation */
.step-fade-enter-active,
.step-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.step-fade-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.step-fade-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}
</style>
