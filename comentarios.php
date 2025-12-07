<?php
session_start();
include_once("conexao.php");

/* =============================
   SEGURANÇA – CSRF TOKEN
   ============================= */
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/* =============================
   PAGINAÇÃO (10 por página)
   ============================= */
$por_pagina = 10;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$inicio = ($pagina - 1) * $por_pagina;

$sql = "SELECT * FROM comentarios_db ORDER BY data DESC LIMIT $inicio, $por_pagina";
$result = mysqli_query($conn, $sql);

$sql_total = "SELECT COUNT(*) AS total FROM comentarios_db";
$total_res = mysqli_query($conn, $sql_total);
$total = mysqli_fetch_assoc($total_res)['total'];
$total_paginas = ceil($total / $por_pagina);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Comentários dos alunos — Decords Música & Teoria">
	<meta name="author" content="Luciano Moraes Rodrigues">
	<title>Decords - Comentários</title>

	<!-- TailwindCSS -->
	<script src="https://cdn.tailwindcss.com"></script>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

	<!-- reCAPTCHA -->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<style>
		body {
			font-family: 'Inter', sans-serif;
		}
	</style>
</head>

<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

	<!-- ========================= -->
	<!-- NAVBAR FIXA PADRÃO       -->
	<!-- ========================= -->
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

	<!-- ========================= -->
	<!-- CONTEÚDO PRINCIPAL        -->
	<!-- ========================= -->
	<div class="pt-32 max-w-7xl mx-auto px-4 w-full flex-grow">

		<h1 class="text-3xl font-bold mb-10 border-b pb-3 text-left">Comentários dos Alunos</h1>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

			<!-- LISTA DE COMENTÁRIOS -->
			<div class="lg:col-span-2 space-y-6">
				<?php if ($total > 0): ?>
					<?php while ($row = mysqli_fetch_assoc($result)): ?>
						<div class="bg-white border shadow rounded-xl p-6">
							<p><strong>Nome:</strong> <?= htmlspecialchars($row['nome']) ?></p>
							<p><strong>Cidade:</strong> <?= htmlspecialchars($row['cidade']) ?></p>
							<p class="mt-3"><strong>Mensagem:</strong><br><?= nl2br(htmlspecialchars($row['mensagem'])) ?></p>
							<p class="text-gray-500 text-sm mt-3">
								<strong>Data:</strong> <?= date("d/m/Y H:i", strtotime($row['data'])) ?>
							</p>
						</div>
					<?php endwhile; ?>
				<?php else: ?>
					<p class="text-gray-600">Nenhum comentário encontrado.</p>
				<?php endif; ?>

				<!-- PAGINAÇÃO -->
				<?php if ($total_paginas > 1): ?>
					<div class="mt-10 flex justify-center">
						<ul class="flex space-x-2">

							<?php if ($pagina > 1): ?>
								<li><a class="px-3 py-2 bg-gray-200 rounded" href="?pagina=<?= $pagina - 1 ?>">← Anterior</a></li>
							<?php endif; ?>

							<?php for ($i = 1; $i <= $total_paginas; $i++): ?>
								<li>
									<a class="px-3 py-2 rounded <?= $i == $pagina ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>"
										href="?pagina=<?= $i ?>"><?= $i ?></a>
								</li>
							<?php endfor; ?>

							<?php if ($pagina < $total_paginas): ?>
								<li><a class="px-3 py-2 bg-gray-200 rounded" href="?pagina=<?= $pagina + 1 ?>">Próxima →</a></li>
							<?php endif; ?>

						</ul>
					</div>
				<?php endif; ?>
			</div>

			<!-- FORMULÁRIO -->
			<div class="bg-white border shadow-xl rounded-xl p-6 h-fit">
				<h2 class="text-xl font-bold mb-4">Enviar Comentário</h2>

				<form action="envia_comentario.php" method="post">
					<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

					<div class="mb-4">
						<label class="block font-semibold mb-1">Nome Completo*</label>
						<input type="text" name="nome" required maxlength="80" class="w-full border rounded px-3 py-2">
					</div>

					<div class="mb-4">
						<label class="block font-semibold mb-1">Cidade*</label>
						<input type="text" name="cidade" required maxlength="60" class="w-full border rounded px-3 py-2">
					</div>

					<div class="mb-4">
						<label class="block font-semibold mb-1">Mensagem*</label>
						<textarea name="mensagem" rows="6" required maxlength="2000" class="w-full border rounded px-3 py-2"></textarea>
					</div>

					<div class="g-recaptcha mb-4" data-sitekey="6Ldy9mIUAAAAANVnYWvVABDsId8Pnw_1kn4Hm85A"></div>

					<button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
						Enviar Comentário
					</button>
				</form>
			</div>

		</div>
	</div>

	<!-- ========================= -->
	<!-- FOOTER FULL-WIDTH REAL   -->
	<!-- ========================= -->
	<footer class="w-full bg-gray-900 text-gray-300 mt-20 pt-12 pb-8">

		<div class="max-w-7xl mx-auto px-6">

			<div class="grid grid-cols-1 md:grid-cols-3 gap-10">

				<div>
					<h3 class="text-white text-xl font-bold mb-4 flex items-center">
						<i class="fas fa-guitar mr-2 text-blue-400"></i> DECORDS
					</h3>
					<p class="text-sm leading-relaxed">
						Comprometidos com a educação musical de qualidade e disseminação do conhecimento.
					</p>
				</div>

				<div class="text-center">
					<h4 class="text-white text-lg font-semibold mb-4">Links úteis</h4>
					<ul class="space-y-2">
						<li><a href="index.php" class="hover:text-blue-400">Início</a></li>
						<li><a href="historico.php" class="hover:text-blue-400">Histórico</a></li>
						<li><a href="professor.php" class="hover:text-blue-400">Professor</a></li>
						<li><a href="contato.php" class="hover:text-blue-400">Contato</a></li>
						<li><a href="fotos.php" class="hover:text-blue-400">Fotos</a></li>
						<li><a href="videos.php" class="hover:text-blue-400">Vídeos</a></li>
					</ul>
				</div>

				<div class="md:text-right text-center">
					<h4 class="text-white font-semibold mb-4">Redes Sociais</h4>
					<div class="flex md:justify-end justify-center space-x-4">
						<a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-gray-700"><i class="fab fa-facebook"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-gray-700"><i class="fab fa-youtube"></i></a>
						<a href="#" class="p-2 bg-gray-800 rounded-full hover:bg-gray-700"><i class="fab fa-blogger"></i></a>
					</div>
				</div>

			</div>

			<div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-400">
				&copy; 2016 - <?= date("Y") ?> Decords • Desenvolvido por
				<span class="text-blue-400 font-semibold">Luciano M. Rodrigues</span>
			</div>

		</div>
	</footer>

</body>

</html>