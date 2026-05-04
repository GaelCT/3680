<script setup>
// import '../styles/login.css'
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');

const validateForm = () => {
    if (!/\S+@\S+\.\S+/.test(email.value)) {
        alert("Invalid email")
        return false
    }
    if (password.value.length < 8) {
        alert("Invalid password")
        return false
    }
    return true
}

const logIn = async () => {
    if (!validateForm()) return

    const res = await fetch('/dro/api/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: email.value, password: password.value })
    })

    const data = await res.json()

    if (res.ok) {
        localStorage.setItem('token', data.token)
        localStorage.setItem('username', data.username)
        localStorage.setItem('userId', data.userId)
        alert('Welcome ' + data.username)
        router.push('/main')
    } else {
        alert(data.error)
    }
}
</script>

    <template>
        <img src="@/assets/dro.png" class="top-logo" alt="logo">
        <div class="container">

            <h1>Log In</h1>
            <form @submit.prevent="logIn">
                <label for="email">Email: </label>
                <input v-model="email"><br>
                <label for="password">Password: </label>
                <input v-model="password"><br>

                <button type="submit">Log in</button>

                <h2>Don't have an account?</h2>
                    <button type="button" @click="$router.push('/signUp')" class="SignUp"> Sign Up </button>
                    <button type="button" @click="$router.push('/main')" class="SignUp"> main</button>
            </form>
        </div>
            
    </template>
