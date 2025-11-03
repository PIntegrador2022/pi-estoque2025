# 📦 Sistema de Estoque com Análise de Dados

[![PHP](https://img.shields.io/badge/PHP-8.x+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![Chart.js](https://img.shields.io/badge/Chart.js-4.x-FF6384?logo=chartdotjs&logoColor=white)](https://www.chartjs.org/)

> **Projeto Integrador UNIVESP** – Plataforma web para gestão inteligente de estoque em pequenos negócios de comércio eletrônico, com dashboards analíticos e previsão de demanda baseada em dados históricos.

---

## 🎯 Objetivo

Substituir planilhas manuais e controles descentralizados por um **sistema integrado, seguro e intuitivo**, capaz de:
- Monitorar estoque em tempo real,
- Registrar entradas e saídas com histórico completo,
- Gerar relatórios analíticos com gráficos interativos,
- Apresentar tendências de demanda (crescente/estável/decrescente),
- Apoiar decisões estratégicas de reposição e gestão.

---

## ✨ Funcionalidades

- 🔐 **Autenticação com níveis de acesso** (admin / usuário)
- 📥 **Entrada de produtos** com registro de data e quantidade
- 📤 **Saída de produtos** com validação de estoque disponível
- 📊 **Dashboard interativo** com cards de:
  - Total de usuários
  - Total de produtos
  - Valor total em estoque
  - Alertas de estoque baixo
- 📈 **Relatórios analíticos**:
  - Consumo por produto
  - Reposição por produto
  - Tendências temporais (MM-YYYY)
  - Valor total movimentado
- 📉 **Análise de Dados** (simulada para apresentação):
  - Classificação de demanda: crescente, estável ou decrescente
  - Top 5 produtos por previsão de saída
  - Gráficos de tendência histórica
- 📤 **Exportação para Excel (.xlsx) e CSV**
- 🗂️ **Gerenciamento de categorias e usuários**
- 📱 **Interface 100% responsiva** (desktop e mobile)

---

## 🛠️ Tecnologias Utilizadas

- **Backend**: PHP 8.x (PDO, sessões, segurança básica)
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5, Chart.js
- **Banco de Dados**: MySQL
- **Gestão de Dependências**: Composer (PhpSpreadsheet para Excel)
- **Metodologia**: Design Thinking (empathy → prototype → test)
- **Hospedagem**: GitHub (código), compatível com XAMPP, WAMP, hospedagem compartilhada

---

## 🚀 Instalação Local

1. **Clone o repositório**
   ```bash
   git clone https://github.com/NOME_DA_ORGANIZACAO/pi-estoque2025.git
   cd pi-estoque2025