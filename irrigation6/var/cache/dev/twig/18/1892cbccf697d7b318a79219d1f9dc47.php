<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* expert/nouveau_produit.html.twig */
class __TwigTemplate_04dcfc3e8ce185f6fb8117df7b867c4c extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'sidebar' => [$this, 'block_sidebar'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context): bool|string|Template|TemplateWrapper
    {
        // line 1
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/nouveau_produit.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/nouveau_produit.html.twig"));

        $this->parent = $this->load("base.html.twig", 1);
        yield from $this->parent->unwrap()->yield($context, array_merge($this->blocks, $blocks));
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

    }

    // line 2
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "Nouveau Produit - AGRIFLOW";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 4
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_sidebar(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "sidebar"));

        // line 5
        yield "    <a href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_home");
        yield "\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_irrigation");
        yield "\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"";
        // line 7
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_diagnostics");
        yield "\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"";
        // line 8
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produits");
        yield "\" class=\"active\"><span class=\"icon\">🧪</span> Produits</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 11
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 12
        yield "<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Ajouter un produit</div>
    <div class=\"page-title\">Nouveau Produit Phytosanitaire</div>
</div>

<div class=\"card\" style=\"max-width:600px;margin:0 auto\">
    <form method=\"post\" id=\"produitForm\">
        <label>Nom du produit</label>
        <input type=\"text\" name=\"nomProduit\" id=\"nomProduit\" required placeholder=\"Ex: Roundup\">
        <div id=\"errNom\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Le nom est obligatoire.
        </div>

        <label>Dosage <span style=\"color:#95a5a6;font-weight:normal;font-size:12px\">(format: nombre ml/L — ex: 3ml/L)</span></label>
        <input type=\"text\" name=\"dosage\" id=\"dosage\" placeholder=\"Ex: 3ml/L\">
        <div id=\"errDosage\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Format invalide. Utilisez le format : <strong>3ml/L</strong> (nombre suivi de ml/L)
        </div>

        <label>Fréquence d'application <span style=\"color:#95a5a6;font-weight:normal;font-size:12px\">(format: nombre par semaine — ex: 3 par semaine)</span></label>
        <input type=\"text\" name=\"frequence\" id=\"frequence\" placeholder=\"Ex: 3 par semaine\">
        <div id=\"errFrequence\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Format invalide. Utilisez le format : <strong>3 par semaine</strong> (nombre suivi de \"par semaine\")
        </div>

        <label>Remarques</label>
        <textarea name=\"remarques\" placeholder=\"Notes supplémentaires...\"></textarea>

        <div style=\"display:flex;justify-content:flex-end;gap:12px;margin-top:25px\">
            <a href=\"";
        // line 41
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produits");
        yield "\" class=\"btn btn-gray\">Annuler</a>
            <button type=\"button\" onclick=\"validerFormulaire()\" class=\"btn btn-green\">💾 Enregistrer</button>
        </div>
    </form>
</div>

<script>
function validerFormulaire() {
    let valide = true;

    // Reset erreurs
    document.getElementById('errNom').style.display = 'none';
    document.getElementById('errDosage').style.display = 'none';
    document.getElementById('errFrequence').style.display = 'none';

    // Valider nom
    const nom = document.getElementById('nomProduit').value.trim();
    if (nom === '') {
        document.getElementById('errNom').style.display = 'block';
        valide = false;
    }

    // Valider dosage : nombre suivi de ml/L (ex: 3ml/L ou 3.5ml/L)
    const dosage = document.getElementById('dosage').value.trim();
    const dosageRegex = /^\\d+(\\.\\d+)?ml\\/L\$/i;
    if (dosage !== '' && !dosageRegex.test(dosage)) {
        document.getElementById('errDosage').style.display = 'block';
        valide = false;
    }

    // Valider fréquence : nombre suivi de \"par semaine\" (ex: 3 par semaine)
    const frequence = document.getElementById('frequence').value.trim();
    const frequenceRegex = /^\\d+\\s+par\\s+semaine\$/i;
    if (frequence !== '' && !frequenceRegex.test(frequence)) {
        document.getElementById('errFrequence').style.display = 'block';
        valide = false;
    }

    if (valide) {
        document.getElementById('produitForm').submit();
    } else {
        // Alerte globale
        alert('⚠️ Veuillez corriger les erreurs avant d\\'enregistrer.\\n\\n• Dosage : format requis → 3ml/L\\n• Fréquence : format requis → 3 par semaine');
    }
}
</script>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "expert/nouveau_produit.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  171 => 41,  140 => 12,  127 => 11,  114 => 8,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Nouveau Produit - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('expert_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('expert_irrigation') }}\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"{{ path('expert_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"{{ path('expert_produits') }}\" class=\"active\"><span class=\"icon\">🧪</span> Produits</a>
{% endblock %}

{% block body %}
<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Ajouter un produit</div>
    <div class=\"page-title\">Nouveau Produit Phytosanitaire</div>
</div>

<div class=\"card\" style=\"max-width:600px;margin:0 auto\">
    <form method=\"post\" id=\"produitForm\">
        <label>Nom du produit</label>
        <input type=\"text\" name=\"nomProduit\" id=\"nomProduit\" required placeholder=\"Ex: Roundup\">
        <div id=\"errNom\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Le nom est obligatoire.
        </div>

        <label>Dosage <span style=\"color:#95a5a6;font-weight:normal;font-size:12px\">(format: nombre ml/L — ex: 3ml/L)</span></label>
        <input type=\"text\" name=\"dosage\" id=\"dosage\" placeholder=\"Ex: 3ml/L\">
        <div id=\"errDosage\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Format invalide. Utilisez le format : <strong>3ml/L</strong> (nombre suivi de ml/L)
        </div>

        <label>Fréquence d'application <span style=\"color:#95a5a6;font-weight:normal;font-size:12px\">(format: nombre par semaine — ex: 3 par semaine)</span></label>
        <input type=\"text\" name=\"frequence\" id=\"frequence\" placeholder=\"Ex: 3 par semaine\">
        <div id=\"errFrequence\" style=\"color:#E74C3C;font-size:12px;margin-top:4px;display:none\">
            Format invalide. Utilisez le format : <strong>3 par semaine</strong> (nombre suivi de \"par semaine\")
        </div>

        <label>Remarques</label>
        <textarea name=\"remarques\" placeholder=\"Notes supplémentaires...\"></textarea>

        <div style=\"display:flex;justify-content:flex-end;gap:12px;margin-top:25px\">
            <a href=\"{{ path('expert_produits') }}\" class=\"btn btn-gray\">Annuler</a>
            <button type=\"button\" onclick=\"validerFormulaire()\" class=\"btn btn-green\">💾 Enregistrer</button>
        </div>
    </form>
</div>

<script>
function validerFormulaire() {
    let valide = true;

    // Reset erreurs
    document.getElementById('errNom').style.display = 'none';
    document.getElementById('errDosage').style.display = 'none';
    document.getElementById('errFrequence').style.display = 'none';

    // Valider nom
    const nom = document.getElementById('nomProduit').value.trim();
    if (nom === '') {
        document.getElementById('errNom').style.display = 'block';
        valide = false;
    }

    // Valider dosage : nombre suivi de ml/L (ex: 3ml/L ou 3.5ml/L)
    const dosage = document.getElementById('dosage').value.trim();
    const dosageRegex = /^\\d+(\\.\\d+)?ml\\/L\$/i;
    if (dosage !== '' && !dosageRegex.test(dosage)) {
        document.getElementById('errDosage').style.display = 'block';
        valide = false;
    }

    // Valider fréquence : nombre suivi de \"par semaine\" (ex: 3 par semaine)
    const frequence = document.getElementById('frequence').value.trim();
    const frequenceRegex = /^\\d+\\s+par\\s+semaine\$/i;
    if (frequence !== '' && !frequenceRegex.test(frequence)) {
        document.getElementById('errFrequence').style.display = 'block';
        valide = false;
    }

    if (valide) {
        document.getElementById('produitForm').submit();
    } else {
        // Alerte globale
        alert('⚠️ Veuillez corriger les erreurs avant d\\'enregistrer.\\n\\n• Dosage : format requis → 3ml/L\\n• Fréquence : format requis → 3 par semaine');
    }
}
</script>
{% endblock %}", "expert/nouveau_produit.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\expert\\nouveau_produit.html.twig");
    }
}
