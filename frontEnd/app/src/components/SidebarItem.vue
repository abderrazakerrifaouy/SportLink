<template>
  <li>
    <RouterLink :to="to || '#'" :class="[
      'flex items-center gap-6 px-6 py-5 border-l-[4px] transition-all duration-200 group',
      active
        ? 'bg-green-50/50 border-[#064E3B] text-[#064E3B] font-extrabold'
        : 'border-transparent text-gray-500 hover:bg-gray-50 hover:text-[#064E3B]'
    ]">
      <i :class="[
        'fa-solid text-xl w-8 text-center transition-transform group-hover:scale-110',
        icon,
        active ? 'text-[#064E3B]' : 'text-gray-400'
      ]"></i>

      <span class="text-base tracking-normal uppercase text-[13px] font-semibold">
        {{ label }}
      </span>
    </RouterLink>
  </li>
</template>

<script setup>
defineProps(['icon', 'label', 'active', 'to'])
</script>
