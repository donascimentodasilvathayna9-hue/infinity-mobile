document.addEventListener("DOMContentLoaded", function () {

    // Validar confirmação de senha

    const senha = document.querySelector('input[name="senha"]');
    const confirmar = document.querySelector('input[name="confirmar"]');

    if (senha && confirmar) {

        function validarSenha() {

            confirmar.setCustomValidity(
                senha.value !== confirmar.value
                    ? "As senhas não coincidem."
                    : ""
            );

        }

        senha.addEventListener("input", validarSenha);
        confirmar.addEventListener("input", validarSenha);

    }

    // Validar quantidade dos produtos

    const quantidades = document.querySelectorAll(
        'input[name="quantidade"]'
    );

    quantidades.forEach(function (campo) {

        campo.addEventListener("input", function () {

            const quantidade = Number(campo.value);
            const estoque = Number(campo.max);

            if (quantidade < 1) {

                campo.setCustomValidity(
                    "Informe uma quantidade válida."
                );

            } else if (campo.max && quantidade > estoque) {

                campo.setCustomValidity(
                    "Quantidade superior ao estoque."
                );

            } else {

                campo.setCustomValidity("");

            }

        });

    });

    // Confirmar exclusão de produtos

    document.querySelectorAll("form").forEach(function (formulario) {

        const acao = formulario.querySelector(
            'input[name="acao"][value="excluir"]'
        );

        if (acao && !formulario.hasAttribute("onsubmit")) {

            formulario.addEventListener("submit", function (evento) {

                if (!confirm("Deseja realmente excluir este produto?")) {

                    evento.preventDefault();

                }

            });

        }

    });

});