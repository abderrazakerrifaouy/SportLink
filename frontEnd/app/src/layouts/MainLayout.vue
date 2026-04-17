<script setup>
    import SidebarItem from '@/components/SidebarItem.vue'
    import HeaderComponents from '@/components/HeaderComponents.vue'
</script>
<template>
    <div class="h-screen bg-[#F3F4F6] font-sans text-gray-900 overflow-hidden flex flex-col">
        <HeaderComponents />
        <div class="flex-1 flex overflow-hidden">
            <aside class="w-80 hidden lg:flex flex-col gap-6 p-10 pr-0 shrink-0 overflow-y-auto">
                <div class="bg-[#064E3B] text-white p-6 rounded-2xl flex items-center gap-4 shadow-xl">
                    <div class="p-3 bg-[#10B981] rounded-xl"><i class="fa-solid fa-medal"></i></div>
                    <div>
                        <p class="font-black text-lg">SportLink Pro</p>
                        <p class="text-[11px] text-green-200 uppercase tracking-widest">Elite</p>
                    </div>
                </div>

                <nav class="bg-white rounded-2xl border border-gray-200 shadow-md">
                    <ul class="flex flex-col">
                        <SidebarItem icon="fa-home" label="Home page" active />
                        <SidebarItem icon="fa-user" label="Mon Profil" />
                        <SidebarItem icon="fa-envelope" label="Messages" />
                        <SidebarItem icon="fa-network-wired" label="Réseau" />
                        <SidebarItem icon="fa-users" label="Mon Equipe" />
                        <!-- <SidebarItem icon="fa-chart-line" label="" /> -->
                    </ul>
                </nav>
            </aside>

            <main class="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #064E3B;
    }
</style>
