import { createRouter, createWebHistory } from 'vue-router'
import Main from '@/pages/main.vue'
import SignUp from '@/pages/signUp.vue'
import LoginPage from '@/pages/login.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "LoginPage",
      component: LoginPage
    },

    // follow this format for other views
    {
      path: "/signup",
      name: "signup",
      component: SignUp
    },
    
    {
      path: "/main",
      name: "main",
      component: Main
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/'
    }



  ],
})

export default router
