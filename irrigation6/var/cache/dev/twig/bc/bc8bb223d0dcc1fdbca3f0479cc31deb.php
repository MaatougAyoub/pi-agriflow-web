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

/* expert/produits.html.twig */
class __TwigTemplate_569eddd95745ff1b1a4c2bbdc9fdda6b extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/produits.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/produits.html.twig"));

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

        yield "Produits - AGRIFLOW";
        
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
        yield "<div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:30px\">
    <div>
        <div class=\"page-subtitle\">Gestion des produits</div>
        <div class=\"page-title\">Produits Phytosanitaires</div>
    </div>
    <a href=\"";
        // line 17
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produit_new");
        yield "\" class=\"btn btn-green\">➕ Nouveau Produit</a>
</div>

<div style=\"display:flex;flex-direction:column;gap:12px\">
    ";
        // line 21
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["produits"]) || array_key_exists("produits", $context) ? $context["produits"] : (function () { throw new RuntimeError('Variable "produits" does not exist.', 21, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
            // line 22
            yield "    <div class=\"card\" style=\"display:flex;align-items:center;padding:15px 20px;
                              border-left:4px solid #2D5A27\">
        <div style=\"flex:1\">
            <div style=\"font-weight:700;font-size:15px;color:#1B4332\">";
            // line 25
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "nomProduit", [], "any", false, false, false, 25), "html", null, true);
            yield "</div>
            ";
            // line 26
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["p"], "remarques", [], "any", false, false, false, 26)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 27
                yield "            <div style=\"font-size:13px;color:#95a5a6;font-style:italic;margin-top:3px\">
                ";
                // line 28
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "remarques", [], "any", false, false, false, 28), "html", null, true);
                yield "
            </div>
            ";
            }
            // line 31
            yield "        </div>
        <div style=\"width:150px;text-align:center\">
            <span style=\"background:#E8F5E9;color:#2D5A27;padding:4px 12px;
                          border-radius:20px;font-size:12px;font-weight:600\">
                ";
            // line 35
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "dosage", [], "any", false, false, false, 35), "html", null, true);
            yield "
            </span>
        </div>
        <div style=\"width:180px;text-align:center;color:#6B7280;font-size:13px\">
            ";
            // line 39
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "frequenceApplication", [], "any", false, false, false, 39), "html", null, true);
            yield "
        </div>
        <div>
            <a href=\"";
            // line 42
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produit_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["p"], "id", [], "any", false, false, false, 42)]), "html", null, true);
            yield "\"
               onclick=\"return confirm('Supprimer ?')\"
               style=\"background:#FDEDEC;color:#E74C3C;padding:8px 14px;
                      border-radius:8px;text-decoration:none;font-size:13px;font-weight:600\">
                🗑 Supprimer
            </a>
        </div>
    </div>
    ";
            $context['_iterated'] = true;
        }
        // line 50
        if (!$context['_iterated']) {
            // line 51
            yield "    <div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
        Aucun produit.
    </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['p'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 55
        yield "</div>
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
        return "expert/produits.html.twig";
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
        return array (  222 => 55,  213 => 51,  211 => 50,  198 => 42,  192 => 39,  185 => 35,  179 => 31,  173 => 28,  170 => 27,  168 => 26,  164 => 25,  159 => 22,  154 => 21,  147 => 17,  140 => 12,  127 => 11,  114 => 8,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Produits - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('expert_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('expert_irrigation') }}\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"{{ path('expert_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"{{ path('expert_produits') }}\" class=\"active\"><span class=\"icon\">🧪</span> Produits</a>
{% endblock %}

{% block body %}
<div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:30px\">
    <div>
        <div class=\"page-subtitle\">Gestion des produits</div>
        <div class=\"page-title\">Produits Phytosanitaires</div>
    </div>
    <a href=\"{{ path('expert_produit_new') }}\" class=\"btn btn-green\">➕ Nouveau Produit</a>
</div>

<div style=\"display:flex;flex-direction:column;gap:12px\">
    {% for p in produits %}
    <div class=\"card\" style=\"display:flex;align-items:center;padding:15px 20px;
                              border-left:4px solid #2D5A27\">
        <div style=\"flex:1\">
            <div style=\"font-weight:700;font-size:15px;color:#1B4332\">{{ p.nomProduit }}</div>
            {% if p.remarques %}
            <div style=\"font-size:13px;color:#95a5a6;font-style:italic;margin-top:3px\">
                {{ p.remarques }}
            </div>
            {% endif %}
        </div>
        <div style=\"width:150px;text-align:center\">
            <span style=\"background:#E8F5E9;color:#2D5A27;padding:4px 12px;
                          border-radius:20px;font-size:12px;font-weight:600\">
                {{ p.dosage }}
            </span>
        </div>
        <div style=\"width:180px;text-align:center;color:#6B7280;font-size:13px\">
            {{ p.frequenceApplication }}
        </div>
        <div>
            <a href=\"{{ path('expert_produit_delete', {id: p.id}) }}\"
               onclick=\"return confirm('Supprimer ?')\"
               style=\"background:#FDEDEC;color:#E74C3C;padding:8px 14px;
                      border-radius:8px;text-decoration:none;font-size:13px;font-weight:600\">
                🗑 Supprimer
            </a>
        </div>
    </div>
    {% else %}
    <div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
        Aucun produit.
    </div>
    {% endfor %}
</div>
{% endblock %}", "expert/produits.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\expert\\produits.html.twig");
    }
}
