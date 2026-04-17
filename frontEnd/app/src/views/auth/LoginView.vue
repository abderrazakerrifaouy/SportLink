<template>
    <AuthLayout activeTab="login" @navigate="(to) => $router.push(to === 'login' ? '/login' : '/register')">
        <h2 class="text-2xl font-semibold text-slate-900">Bon retour.</h2>
        <p class="mt-2 text-sm text-slate-500">
            Accédez à votre tableau de bord de performance.
        </p>


        <div v-if="errorMessage" class="mt-4 rounded-md bg-red-100 px-4 py-2 text-sm text-red-700">
            {{ errorMessage }}
        </div>

        <form class="mt-6 space-y-5" @submit.prevent="onSubmit">

            <div>
                <label class="text-[11px] font-semibold tracking-wide text-slate-500">
                    E-MAIL PROFESSIONNEL
                </label>
                <div class="mt-2">
                    <input v-model="email" type="email" placeholder="nom@club.com"
                        class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/15" />
                </div>
            </div>


            <div>
                <div class="flex items-center justify-between">
                    <label class="text-[11px] font-semibold tracking-wide text-slate-500">
                        MOT DE PASSE
                    </label>
                    <a class="text-[11px] font-semibold text-slate-500 hover:text-emerald-700" href="#">
                        OUBLIÉ ?
                    </a>
                </div>

                <div class="mt-2">
                    <input v-model="password" type="password" placeholder="••••••••"
                        class="w-full rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/15" />
                </div>
            </div>

            <!-- BUTTON -->
            <button type="submit" :disabled="isLoading"
                class="group inline-flex w-full items-center justify-center gap-2 rounded-md bg-emerald-800 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-900 disabled:opacity-50">
                <span v-if="isLoading">Chargement...</span>
                <span v-else>SE CONNECTER →</span>
            </button>

            <!-- REGISTER -->
            <div class="mt-8">
                <p class="text-xs text-slate-500">Nouveau sur SportLink ?</p>

                <div class="mt-3 grid grid-cols-2 gap-3">
                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="$router.push('/register?type=player')">

                        JOUEUR
                    </button>

                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        @click="$router.push('/register?type=club')">
                        CLUB
                    </button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>



<script setup>
    import {
        ref
    } from 'vue'
    import {
        useAuthStore
    } from '@/stores/AuthStore'
    import AuthLayout from '@/layouts/AuthLayout.vue'

    const authStore = useAuthStore()

    const email = ref('')
    const password = ref('')
    const isLoading = ref(false)
    const errorMessage = ref('')

    const onSubmit = async () => {
        if (!email.value || !password.value) {
            errorMessage.value = "Veuillez remplir tous les champs."
            return
        }

        isLoading.value = true
        errorMessage.value = ''

        try {
            await authStore.login(email.value, password.value)
        } catch (error) {
            errorMessage.value = "E-mail ou mot de passe incorrect."
            console.error('Login error:', error)
        } finally {
            isLoading.value = false
        }
    }
</script>

<!-- <script setup>
    import {
        ref
    } from 'vue'
    import {
        useRouter
    } from 'vue-router'

    const router = useRouter()

    const email = ref('')
    const password = ref('')
    const isLoading = ref(false)
    const errorMessage = ref('')

    const onSubmit = async () => {
        // 1) validation بسيطة
        if (!email.value || !password.value) {
            errorMessage.value = "Veuillez remplir tous les champs."
            return
        }

        isLoading.value = true
        errorMessage.value = ''

        try {
            // 2) نعيطو للـ API
            const res = await fetch(`http://localhost:3000/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // زيد هاد السطر إلا كان عندك cookies/session
                    // 'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email: email.value,
                    password: password.value,

                }),

            })

            // 3) نقراو الرد
            const data = await res.json().catch(() => ({}))

            // 4) errors
            if (!res.ok) {
                // backend كيقدر يرجع msg مختلفة
                errorMessage.value =
                    data?.message ||
                    data?.error ||
                    "Email ou mot de passe incorrect."
                return
            }

            // 5) success: خزّن token/user (على حسب شنو كيرجع backend)
            // مثال شائع: { token: "...", user: {...} }
            if (data?.token) localStorage.setItem('token', data.token)
            if (data?.user) localStorage.setItem('user', JSON.stringify(data.user))

            // 6) redirect
            await router.push('/dashboard')
        } catch (err) {
            errorMessage.value = "Problème de connexion au serveur. Réessayez."
        } finally {
            isLoading.value = false
        }
    }
</script> -->
