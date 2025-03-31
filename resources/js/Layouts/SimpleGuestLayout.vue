<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex code-background">
        <!-- Animated Code Background -->
        <div class="code-overlay"></div>
        
        <!-- Floating Code Elements -->
        <div class="floating-code-container">
            <div class="code-snippet code-1">
                <pre><code>function createCourse(title, modules) {
  return {
    id: generateUniqueId(),
    title,
    modules,
    createdAt: new Date(),
    students: []
  };
}</code></pre>
            </div>
            
            <div class="code-snippet code-2">
                <pre><code>const modules = [
  { id: 1, title: "HTML Basics" },
  { id: 2, title: "CSS Fundamentals" },
  { id: 3, title: "JavaScript Essentials" }
];</code></pre>
            </div>
            
            <div class="code-snippet code-3">
                <pre><code>class Student {
  constructor(name, email) {
    this.name = name;
    this.email = email;
    this.enrolledCourses = [];
  }
  
  enroll(courseId) {
    this.enrolledCourses.push(courseId);
  }
}</code></pre>
            </div>
            
            <div class="code-snippet code-4">
                <pre><code>const calculateProgress = (student, course) => {
  const completedModules = student.progress
    .filter(p => p.courseId === course.id)
    .length;
    
  return (completedModules / course.modules.length) * 100;
};</code></pre>
            </div>
            
            <div class="code-snippet code-5">
                <pre><code>async function submitExercise(studentId, exerciseId, answers) {
  try {
    const result = await api.post('/exercises/submit', {
      studentId,
      exerciseId,
      answers
    });
    return result.data.score;
  } catch (error) {
    console.error('Submission failed:', error);
  }
}</code></pre>
            </div>
            
            <div class="code-snippet code-6">
                <pre><code>const generateReport = (courseId) => {
  const students = getEnrolledStudents(courseId);
  const averageScore = students.reduce((sum, student) => {
    return sum + student.averageScore;
  }, 0) / students.length;
  
  return {
    courseId,
    studentCount: students.length,
    averageScore,
    completionRate: getCompletionRate(courseId)
  };
};</code></pre>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex flex-col items-center justify-center w-full z-10">
            <div class="logo-container mb-6">
                <Link href="/">
                    <ApplicationLogo class="w-auto h-32" />
                </Link>
            </div>
            
            <div class="form-container w-full max-w-md mx-auto bg-white/10 backdrop-blur-md p-8 rounded-xl shadow-lg border border-white/20">
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.code-background {
    @apply bg-gray-900 relative overflow-hidden;
    background-image: 
        linear-gradient(to bottom, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.8)),
        url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23232f45' fill-opacity='0.15'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.code-overlay {
    @apply absolute inset-0 z-0;
    background: repeating-linear-gradient(
        0deg,
        rgba(26, 32, 44, 0.1),
        rgba(26, 32, 44, 0.1) 1px,
        transparent 1px,
        transparent 2px
    );
}

.floating-code-container {
    @apply absolute inset-0 overflow-hidden pointer-events-none;
}

.code-snippet {
    @apply absolute p-4 rounded-lg;
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
    font-family: 'Fira Code', 'Courier New', monospace;
    font-size: 0.7rem;
    line-height: 1.4;
    max-width: 300px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(5px);
    animation-duration: 30s;
    animation-iteration-count: infinite;
    animation-timing-function: ease-in-out;
}

.code-1 {
    top: 10%;
    left: 5%;
    animation-name: float-1;
}

.code-2 {
    top: 15%;
    right: 10%;
    animation-name: float-2;
}

.code-3 {
    top: 40%;
    left: 8%;
    animation-name: float-3;
}

.code-4 {
    bottom: 30%;
    right: 5%;
    animation-name: float-4;
}

.code-5 {
    bottom: 10%;
    left: 15%;
    animation-name: float-5;
}

.code-6 {
    top: 60%;
    right: 15%;
    animation-name: float-6;
}

.logo-container {
    @apply relative z-10 flex justify-center;
}

.form-container {
    @apply relative z-10;
}

/* Syntax highlighting */
code .keyword {
    color: #ff79c6;
}

code .function {
    color: #50fa7b;
}

code .string {
    color: #f1fa8c;
}

code .number {
    color: #bd93f9;
}

code .comment {
    color: #6272a4;
}

/* Float animations */
@keyframes float-1 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(20px, 10px) rotate(2deg);
    }
    50% {
        transform: translate(10px, 20px) rotate(0deg);
    }
    75% {
        transform: translate(-10px, 10px) rotate(-2deg);
    }
}

@keyframes float-2 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(-15px, 15px) rotate(-1deg);
    }
    50% {
        transform: translate(-20px, 5px) rotate(-2deg);
    }
    75% {
        transform: translate(-5px, -10px) rotate(-1deg);
    }
}

@keyframes float-3 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(15px, -10px) rotate(1deg);
    }
    50% {
        transform: translate(25px, 0px) rotate(2deg);
    }
    75% {
        transform: translate(10px, 10px) rotate(1deg);
    }
}

@keyframes float-4 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(-20px, -5px) rotate(-1deg);
    }
    50% {
        transform: translate(-10px, -15px) rotate(-2deg);
    }
    75% {
        transform: translate(5px, -10px) rotate(-1deg);
    }
}

@keyframes float-5 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(10px, 15px) rotate(1deg);
    }
    50% {
        transform: translate(0px, 25px) rotate(0deg);
    }
    75% {
        transform: translate(-10px, 15px) rotate(-1deg);
    }
}

@keyframes float-6 {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(-15px, -10px) rotate(-1deg);
    }
    50% {
        transform: translate(-5px, -20px) rotate(0deg);
    }
    75% {
        transform: translate(10px, -15px) rotate(1deg);
    }
}

/* Orange highlight for code syntax */
::selection {
    background: rgba(249, 115, 22, 0.3);
    color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .code-snippet {
        font-size: 0.6rem;
        max-width: 200px;
    }
}
</style>
