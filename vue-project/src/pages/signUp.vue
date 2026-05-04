<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const firstName = ref('');
const lastName = ref('');
const username = ref('');
const email = ref('');
const password = ref('');

const validateForm = () => {
    if (!firstName.value || !lastName.value || !username.value) {
        alert("Name and username is needed")
        return false
    }
    if (!/\S+@\S+\.\S+/.test(email.value)) {
        alert("Invalid email")
        return false
    }
    if (password.value.length < 8) {
        alert("Invalid password, Your password must be at least 8 characters long")
        return false
    }
    return true
}

const signUp = async () => {
    if (!validateForm()) return

    const res = await fetch('/dro/api/signup.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            firstName: firstName.value,
            lastName: lastName.value,
            username: username.value,
            email: email.value,
            password: password.value
        })        
    })
    const data = await res.json()

    if (res.ok) {
        alert('Account created! Please log in')
        router.push('/')
    } else { 
        alert (data.error)
    }

}
</script>

<template>
    <div class="container">
        <h1>Sign Up!</h1>
        <form @submit.prevent="signUp">
            <label for="firstName">First Name: </label>
            <input v-model="firstName" type="text"><br>

            <label for="lastName">Last Name: </label>
            <input v-model="lastName" type="text"><br>

            <label for="username">Username: </label>
            <input v-model="username" type="text"><br>

            <label for="email">Email: </label>
            <input v-model="email" type="email"><br>

            <label for="password">Password: </label>
            <input v-model="password" type="password"><br>

            <button type="submit">Sign Up!</button>
            <br>
            <br>
            <h2>Already have an account?</h2>
            <button type="button" @click="$router.push('/')">  Log In </button>
            <br>
        </form>
    </div>
</template>