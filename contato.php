<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Perfil e biografia do Professor José A. Homrich - Projeto Decords Música e Teoria.">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Professor</title>

	<!-- Tailwind CSS -->
	<script src="https://cdn.tailwindcss.com"></script>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<style>
		body {
			font-family: 'Inter', sans-serif;
		}

		/* Hover Dropdown Desktop */
		@media (min-width: 768px) {
			.group:hover .group-hover\:block {
				display: block;
			}
		}

		/* Estilo da biografia */
		#professor-bio p {
			text-indent: 50px;
			text-align: justify;
			margin-bottom: 1.5rem;
			line-height: 1.75;
		}
	</style>
</head>
<!-- ADJUSTE IMPORTANTE -->
<!-- pt-24 = compensação EXATA da navbar fixa (altura real ~ 96px) -->

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen pt-24">

	<!-- NAVBAR FIXA E SEM ESPAÇOS -->
	<nav class="bg-blue-900 text-white shadow-lg fixed top-0 left-0 w-full z-50">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-20 items-center">

				<!-- LOGO -->
				<div class="flex-shrink-0 flex items-center gap-3">
					<a href="index.php" class="flex items-center gap-3 group">
						<img src="img/foto22.jpg"
							class="h-12 w-auto rounded-lg border-2 border-blue-400 group-hover:border-white transition object-cover">
						<span class="text-xl font-bold tracking-wide">DECORDS</span>
					</a>
				</div>

				<!-- MENU DESKTOP -->
				<div class="hidden md:flex items-center space-x-6">
					<!-- Dropdown Categorias -->
					<div class="relative group h-full py-6 cursor-pointer">
						<span class="hover:text-blue-300 font-medium flex items-center transition">
							Categorias <i class="fas fa-chevron-down ml-1 text-xs"></i>
						</span>
						<!-- Submenu -->
						<div class="absolute top-16 left-0 w-48 bg-white text-gray-800 shadow-xl rounded-b-md hidden group-hover:block border-t-4 border-blue-500 animate-fade-in">
							<a href="historico.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-history mr-2 text-blue-500"></i>Histórico</a>
							<a href="professor.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-chalkboard-teacher mr-2 text-blue-500"></i>Professor</a>
							<a href="fotos.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition"><i class="fas fa-images mr-2 text-blue-500"></i>Fotos</a>
							<a href="videos.php" class="block px-4 py-3 hover:bg-gray-100 transition"><i class="fas fa-video mr-2 text-blue-500"></i>Vídeos</a>
						</div>
					</div>

					<a href="contato.php" class="hover:text-blue-300 font-medium transition">Contato/Agenda</a>

					<!-- Dropdown Exercícios -->
					<div class="relative group h-full py-6 cursor-pointer">
						<span class="hover:text-blue-300 font-medium flex items-center transition">
							Exercícios <i class="fas fa-chevron-down ml-1 text-xs"></i>
						</span>

						<div class="absolute top-16 left-0 w-56 bg-white text-gray-800 shadow-xl rounded-b-md hidden group-hover:block border-t-4 border-blue-500">
							<a href="login.php" class="block px-4 py-3 hover:bg-gray-100 border-b border-gray-100 transition">
								<i class="fas fa-user-graduate mr-2 text-blue-600"></i>Sou Aluno/Usuário
							</a>
							<a href="login_professor.php" class="block px-4 py-3 hover:bg-gray-100 transition">
								<i class="fas fa-chalkboard-teacher mr-2 text-blue-600"></i>Sou Professor
							</a>
						</div>
					</div>

					<a href="comentarios.php" class="hover:text-blue-300 font-medium transition">Comentários</a>
				</div>

				<!-- ÍCONES SOCIAIS -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="#" class="text-gray-300 hover:text-white transition"><i class="fab fa-facebook text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-red-500 transition"><i class="fab fa-youtube text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-orange-400 transition"><i class="fab fa-blogger text-xl"></i></a>
				</div>

				<!-- Botão Mobile -->
				<div class="md:hidden">
					<button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
						<i class="fas fa-bars text-2xl"></i>
					</button>
				</div>

			</div>
		</div>

		<!-- MENU MOBILE -->
		<div id="mobile-menu" class="hidden md:hidden bg-blue-800 border-t border-blue-700">
			<div class="px-4 py-4 space-y-2">

				<p class="text-gray-300 uppercase text-xs">Navegação</p>
				<a href="historico.php" class="block py-2 px-2 text-white">Histórico</a>
				<a href="professor.php" class="block py-2 px-2 text-white bg-blue-700 font-bold">Professor</a>
				<a href="fotos.php" class="block py-2 px-2 text-white">Fotos</a>
				<a href="videos.php" class="block py-2 px-2 text-white">Vídeos</a>
				<a href="contato.php" class="block py-2 px-2 text-white">Contato/Agenda</a>

				<p class="text-gray-300 uppercase text-xs mt-2">Acesso</p>
				<a href="login.php" class="block py-2 px-2 text-white">Aluno</a>
				<a href="login_professor.php" class="block py-2 px-2 text-white">Professor</a>
			</div>
		</div>
	</nav>

	<!-- CONTEÚDO -->
	<main class="max-w-7xl mx-auto px-6 py-10">

		<h1 class="text-3xl font-bold text-blue-900 mb-8 border-b pb-2">
			📍 Localização e Informações de Contato
		</h1>

		<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

			<!-- MAPA -->
			<div>
				<h2 class="text-2xl font-semibold mb-4">Estamos Aqui</h2>

				<div class="w-full h-80 rounded-xl overflow-hidden shadow-lg border">
					<iframe
						src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3458.6626103729345!2d-51.22383738488834!3d-29.902820781934093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x951964dff354bb83%3A0xda3119108c44ac52!2sEscola+Ministro+Rubem+Carlos+Ludwig!5e0!3m2!1spt-BR!2sbr!4v1465962532071"
						width="100%" height="100%" frameborder="0" allowfullscreen loading="lazy">
					</iframe>
				</div>

				<div class="mt-6 p-5 bg-white shadow rounded-lg border">
					<h3 class="text-xl font-bold text-blue-800 mb-2">📌 Escola Ministro Rubem Carlos Ludwig</h3>
					<p class="text-gray-700">
						Rua São João Batista,
						Bairro Mathias Velho – Canoas / RS
					</p>
					<p class="mt-2 text-gray-700">
						<i class="fas fa-map-marker-alt text-red-600"></i>
						Local onde ocorrem as aulas e atividades do Projeto Decords.
					</p>
				</div>
			</div>

			<!-- FORMULÁRIO (sem envio de e-mail, apenas demonstração local) -->
			<div>
				<h2 class="text-2xl font-semibold mb-4">Envie uma Mensagem</h2>

				<form class="bg-white p-6 rounded-xl shadow-lg border space-y-5" method="POST" onsubmit="return false;">

					<div>
						<label for="nome" class="font-semibold">Nome *</label>
						<input type="text" id="nome" name="nome"
							class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-400"
							placeholder="Seu nome completo" required>
					</div>

					<div>
						<label for="email" class="font-semibold">Email *</label>
						<input type="email" id="email" name="email"
							class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-400"
							placeholder="Seu email" required>
					</div>

					<div>
						<label for="mensagem" class="font-semibold">Mensagem *</label>
						<textarea id="mensagem" name="mensagem" rows="5"
							class="w-full mt-1 p-3 border rounded-lg focus:ring-2 focus:ring-blue-400"
							placeholder="Escreva sua mensagem..." required></textarea>
					</div>

					<button type="submit"
						class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-lg transition">
						Enviar Mensagem
					</button>

					<p class="text-sm text-gray-500 mt-2">
						⚠ Este formulário é apenas ilustrativo. O envio real de mensagens foi desativado.
					</p>
				</form>
			</div>

		</div>
	</main>
	<!-- Footer (Padrão Decords) -->
	<footer class="bg-gray-900 text-gray-300 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">

				<div>
					<h3 class="text-white text-lg font-bold mb-4"><i class="fas fa-guitar mr-2"></i>DECORDS</h3>
					<p class="text-sm">Comprometidos com a educação musical de qualidade.</p>
				</div>

				<div class="text-center">
					<h4 class="text-white text-lg font-semibold mb-4">Links úteis</h4>
					<ul class="space-y-2">
						<li><a href="index.php" class="hover:text-blue-400">Início</a></li>
						<li><a href="historico.php" class="hover:text-blue-400">Histórico</a></li>
						<li><a href="professor.php" class="hover:text-blue-400">Professor</a></li>
						<li><a href="contato.php" class="hover:text-blue-400">Contato</a></li>
						<li><a href="fotos.php" class="hover:text-blue-400 transition">Fotos</a></li>
						<li><a href="videos.php" class="hover:text-blue-400 transition">Videos</a></li>
					</ul>
				</div>

				<div class="text-right">
					<h4 class="text-white font-semibold mb-4">Redes Sociais</h4>
					<div class="flex justify-end space-x-4">
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-facebook"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-youtube"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full"><i class="fab fa-blogger"></i></a>
					</div>
				</div>

			</div>
			<div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
				&copy; 2016 - <?= date("Y") ?> Decords • Desenvolvido por
				<span class="text-blue-400 font-semibold">Luciano M. Rodrigues</span>
			</div>
		</div>
	</footer>
</body>

</html>