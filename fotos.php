<?php
// Não é necessário session_start() nesta página, mas mantido para consistência de cabeçalho PHP
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Galeria de fotos do Projeto Decords, mostrando eventos e aulas de música.">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Fotos</title>

	<!-- Bootstrap 4 (para compatibilidade geral e mobile apps) -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

		/* Estilo de Parallax real */
		.parallax-slide {
			background-attachment: fixed;
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
		}

		/* Dropdown desktop */
		@media (min-width: 768px) {
			.group:hover .group-hover\:block {
				display: block;
			}
		}
	</style>
</head>

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen pt-24">

	<!-- NAVBAR -->
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
					<a href="comentarios.php" class="hover:text-blue-300 font-medium">Comentários</a>
				</div>

				<!-- ÍCONES -->
				<div class="hidden md:flex items-center space-x-4 border-l border-blue-700 pl-6">
					<a href="#" class="text-gray-300 hover:text-white"><i class="fab fa-facebook text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-red-500"><i class="fab fa-youtube text-xl"></i></a>
					<a href="#" class="text-gray-300 hover:text-orange-400"><i class="fab fa-blogger text-xl"></i></a>
				</div>

				<!-- Mobile -->
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
				<a href="historico.php" class="block py-2 text-white">Histórico</a>
				<a href="professor.php" class="block py-2 text-white">Professor</a>
				<a href="fotos.php" class="block py-2 text-white font-bold bg-blue-700">Fotos</a>
				<a href="videos.php" class="block py-2 text-white">Vídeos</a>
				<a href="contato.php" class="block py-2 text-white">Contato</a>
				<a href="login.php" class="block py-2 text-white">Aluno</a>
				<a href="login_professor.php" class="block py-2 text-white">Professor</a>
			</div>
		</div>

	</nav>

	<!-- CONTEÚDO -->
	<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex-grow">

		<header class="text-center mb-12">
			<h1 class="text-4xl font-extrabold text-blue-900 mb-2">Galeria de Fotos</h1>
			<p class="text-gray-600 text-lg">Momentos especiais do Projeto Decords</p>
			<div class="w-24 h-1 bg-blue-600 mx-auto mt-4"></div>
		</header>

		<!-- CARROSSEL / PARALLAX -->
		<div id="carousel" class="relative overflow-hidden rounded-xl shadow-2xl mb-16">

			<div id="carousel-inner" class="whitespace-nowrap transition-transform duration-700 ease-in-out">

				<!-- Slides criados abaixo via PHP -->
				<?php
				$fotos = [
					"img/foto1.jpg",
					"img/foto2.jpg",
					"img/foto3.jpg",
					"img/foto4.jpg",
					"img/foto5.jpg",
					"img/foto6.jpg",
					"img/foto8.jpg",
					"img/foto10.jpg",
					"img/foto11.jpg",
					"img/foto12.jpg",
					"img/foto14.jpg",
					"img/foto15.jpg",
					"img/foto16.jpg",
					"img/foto18.jpg",
					"img/foto19.jpg",
					"img/foto28.jpg",
					"img/foto30.jpg",
					"img/foto31.jpg",
					"img/foto32.jpg",
					"img/foto34.jpg",
					"img/foto35.jpg",
					"img/foto36.jpg",
					"img/foto37.jpg",
					"img/foto38.jpg",
					"img/foto39.jpg",
					"img/foto40.jpg"
				];

				foreach ($fotos as $foto) {
					echo "
                    <div class='inline-block w-full h-[450px] md:h-[600px] parallax-slide'
                        style=\"background-image: url('$foto');\">
                        <div class='w-full h-full bg-black/40 flex items-center justify-center'>
                            <p class='text-white text-3xl md:text-4xl font-bold drop-shadow-xl'>
                                Projeto Decords
                            </p>
                        </div>
                    </div>
                ";
				}
				?>

			</div>

			<!-- CONTROLES -->
			<button id="prev" class="absolute top-1/2 left-4 bg-black/40 p-3 rounded-full text-white hover:bg-black/70">
				<i class="fas fa-chevron-left"></i>
			</button>

			<button id="next" class="absolute top-1/2 right-4 bg-black/40 p-3 rounded-full text-white hover:bg-black/70">
				<i class="fas fa-chevron-right"></i>
			</button>

		</div>

	</main>

	<!-- FOOTER -->
	<footer class="bg-gray-900 text-gray-300 py-12 mt-auto">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">

				<div>
					<h3 class="text-white text-lg font-bold mb-3">
						<i class="fas fa-guitar mr-2"></i> DECORDS
					</h3>
					<p class="text-gray-400 text-sm">
						Formando músicos e cidadãos através da arte e educação musical.
					</p>
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

				<div class="md:text-right">
					<h4 class="text-white text-lg font-semibold mb-4">Redes</h4>
					<div class="flex md:justify-end space-x-4">
						<a href="https://www.facebook.com/decordsoficial/" class="p-2 bg-gray-800 rounded-full hover:bg-blue-600"><i class="fab fa-facebook-f"></i></a>
						<a href="https://www.youtube.com/channel/UCYlKeJvPE7jUXpZMNaVtG1A" class="p-2 bg-gray-800 rounded-full hover:bg-red-600"><i class="fab fa-youtube"></i></a>
						<a href="http://ademirhomrichmusica.blogspot.com.br" class="p-2 bg-gray-800 rounded-full hover:bg-orange-600"><i class="fab fa-blogger"></i></a>
					</div>
				</div>

			</div>

			<div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-500">
				&copy; 2016 - <?= date("Y") ?> Decords • Desenvolvido por
				<span class="text-blue-400 font-semibold">Luciano M. Rodrigues</span>
			</div>

		</div>
	</footer>

	<!-- JS -->
	<script>
		let index = 0
		const slider = document.getElementById("carousel-inner")
		const total = <?= count($fotos) ?>;

		function updateSlide() {
			slider.style.transform = `translateX(-${index * 100}%)`
		}

		document.getElementById("next").onclick = function() {
			index = (index + 1) % total
			updateSlide()
		}

		document.getElementById("prev").onclick = function() {
			index = (index - 1 + total) % total
			updateSlide()
		}

		// autoplay
		setInterval(() => {
			index = (index + 1) % total
			updateSlide()
		}, 5000)
	</script>

</body>

</html>