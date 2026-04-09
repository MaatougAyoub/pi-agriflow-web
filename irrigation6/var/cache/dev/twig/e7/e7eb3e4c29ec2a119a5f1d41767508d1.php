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

/* agriculteur/irrigation.html.twig */
class __TwigTemplate_c897d2a3628ac63f2f9d3b48011fd910 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/irrigation.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/irrigation.html.twig"));

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

        yield "Plan d'Irrigation - AGRIFLOW";
        
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
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_home");
        yield "\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"";
        // line 6
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_irrigation");
        yield "\" class=\"active\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"";
        // line 7
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_diagnostics");
        yield "\"><span class=\"icon\">📝</span> Diagnostic</a>
";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 10
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

        // line 11
        yield "<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Farm Management</div>
    <div class=\"page-title\">Plans d'Irrigation</div>
</div>

<div style=\"display:flex;flex-direction:column;gap:12px;\">
    ";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["plans"]) || array_key_exists("plans", $context) ? $context["plans"] : (function () { throw new RuntimeError('Variable "plans" does not exist.', 17, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
            // line 18
            yield "    <a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_irrigation_detail", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["p"], "id", [], "any", false, false, false, 18)]), "html", null, true);
            yield "\"
       style=\"text-decoration:none;color:inherit;\">
        <div style=\"background:white;border-radius:12px;padding:20px 25px;
                    display:flex;justify-content:space-between;align-items:center;
                    box-shadow:0 2px 8px rgba(0,0,0,0.06);
                    border-left:4px solid #52B788;
                    transition:all 0.2s;cursor:pointer;\"
             onmouseover=\"this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)';this.style.transform='translateY(-1px)'\"
             onmouseout=\"this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'\">
            <div>
                <div style=\"font-size:17px;font-weight:700;color:#1B4332;margin-bottom:4px\">
                    ";
            // line 29
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["p"], "culture", [], "any", false, false, false, 29), "nom", [], "any", false, false, false, 29), "html", null, true);
            yield "
                </div>
                <div style=\"font-size:13px;color:#95a5a6\">
                    Parcelle: ";
            // line 32
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["p"], "culture", [], "any", false, false, false, 32), "superficie", [], "any", false, false, false, 32), "html", null, true);
            yield "
                </div>
            </div>
            <div style=\"display:flex;align-items:center;gap:20px\">
                <span class=\"badge badge-green\">";
            // line 36
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "statut", [], "any", false, false, false, 36), "html", null, true);
            yield "</span>
                <div style=\"font-size:16px;font-weight:700;color:#1976D2\">
                    ";
            // line 38
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "besoinEau", [], "any", false, false, false, 38), 1), "html", null, true);
            yield " mm
                </div>
                <div style=\"color:#ccc;font-size:20px\">›</div>
            </div>
        </div>
    </a>
    ";
            $context['_iterated'] = true;
        }
        // line 44
        if (!$context['_iterated']) {
            // line 45
            yield "    <div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
        Aucun plan d'irrigation.
    </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['p'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 49
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
        return "agriculteur/irrigation.html.twig";
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
        return array (  204 => 49,  195 => 45,  193 => 44,  182 => 38,  177 => 36,  170 => 32,  164 => 29,  149 => 18,  144 => 17,  136 => 11,  123 => 10,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Plan d'Irrigation - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('agriculteur_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('agriculteur_irrigation') }}\" class=\"active\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"{{ path('agriculteur_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostic</a>
{% endblock %}

{% block body %}
<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Farm Management</div>
    <div class=\"page-title\">Plans d'Irrigation</div>
</div>

<div style=\"display:flex;flex-direction:column;gap:12px;\">
    {% for p in plans %}
    <a href=\"{{ path('agriculteur_irrigation_detail', {id: p.id}) }}\"
       style=\"text-decoration:none;color:inherit;\">
        <div style=\"background:white;border-radius:12px;padding:20px 25px;
                    display:flex;justify-content:space-between;align-items:center;
                    box-shadow:0 2px 8px rgba(0,0,0,0.06);
                    border-left:4px solid #52B788;
                    transition:all 0.2s;cursor:pointer;\"
             onmouseover=\"this.style.boxShadow='0 4px 16px rgba(0,0,0,0.12)';this.style.transform='translateY(-1px)'\"
             onmouseout=\"this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)';this.style.transform='translateY(0)'\">
            <div>
                <div style=\"font-size:17px;font-weight:700;color:#1B4332;margin-bottom:4px\">
                    {{ p.culture.nom }}
                </div>
                <div style=\"font-size:13px;color:#95a5a6\">
                    Parcelle: {{ p.culture.superficie }}
                </div>
            </div>
            <div style=\"display:flex;align-items:center;gap:20px\">
                <span class=\"badge badge-green\">{{ p.statut }}</span>
                <div style=\"font-size:16px;font-weight:700;color:#1976D2\">
                    {{ p.besoinEau|number_format(1) }} mm
                </div>
                <div style=\"color:#ccc;font-size:20px\">›</div>
            </div>
        </div>
    </a>
    {% else %}
    <div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
        Aucun plan d'irrigation.
    </div>
    {% endfor %}
</div>
{% endblock %}", "agriculteur/irrigation.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\agriculteur\\irrigation.html.twig");
    }
}
