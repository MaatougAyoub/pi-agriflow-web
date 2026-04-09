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

/* agriculteur/diagnostics.html.twig */
class __TwigTemplate_bd9caa5e61a3fa4eb81ae55e4d5d133c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/diagnostics.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/diagnostics.html.twig"));

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

        yield "Diagnostics - AGRIFLOW";
        
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
        yield "\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"";
        // line 7
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_diagnostics");
        yield "\" class=\"active\"><span class=\"icon\">📝</span> Diagnostic</a>
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
        yield "<div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:30px\">
    <div>
        <div class=\"page-subtitle\">Suivi des analyses</div>
        <div class=\"page-title\">Mes Diagnostics</div>
    </div>
    <a href=\"";
        // line 16
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_diagnostic_new");
        yield "\" class=\"btn btn-green\">➕ Nouveau Diagnostic</a>
</div>

<div class=\"card\">
    ";
        // line 20
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["diagnostics"]) || array_key_exists("diagnostics", $context) ? $context["diagnostics"] : (function () { throw new RuntimeError('Variable "diagnostics" does not exist.', 20, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["d"]) {
            // line 21
            yield "    <div style=\"display:flex;align-items:center;padding:15px 20px;
                border-bottom:1px solid #F8F8F8;background:white;
                transition:background 0.2s;\"
         onmouseover=\"this.style.background='#FDFDFD'\"
         onmouseout=\"this.style.background='white'\">

        <div style=\"width:80px\">
            <div style=\"width:45px;height:45px;background:#E8F5E9;border-radius:12px;
                        display:flex;align-items:center;justify-content:center;font-size:20px\">
                🌿
            </div>
        </div>

        <div style=\"flex:1;min-width:0\">
            <div style=\"font-weight:700;font-size:14px;color:#374151\">
                ";
            // line 36
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "nomCulture", [], "any", false, false, false, 36), "html", null, true);
            yield "
            </div>
            <div style=\"font-size:13px;color:#6B7280;margin-top:2px\">
                ";
            // line 39
            yield (((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["d"], "description", [], "any", false, false, false, 39)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((Twig\Extension\CoreExtension::slice($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["d"], "description", [], "any", false, false, false, 39), 0, 60) . "..."), "html", null, true)) : ("Aucune description"));
            yield "
            </div>
        </div>

        <div style=\"width:200px;font-size:13px;color:#6B7280\">
            ";
            // line 44
            yield (((CoreExtension::getAttribute($this->env, $this->source, $context["d"], "reponseExpert", [], "any", true, true, false, 44) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, $context["d"], "reponseExpert", [], "any", false, false, false, 44)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "reponseExpert", [], "any", false, false, false, 44), "html", null, true)) : ("⏳ En attente de réponse"));
            yield "
        </div>

    <div style=\"width:120px;text-align:center\">
        ";
            // line 48
            if (((CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 48) == "traite") || (CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 48) == "traité"))) {
                // line 49
                yield "            <span style=\"padding:5px 12px;border-radius:15px;font-weight:700;font-size:11px;background:#E8F5E9;color:#4CAF50;\">
                ";
                // line 50
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 50)), "html", null, true);
                yield "
            </span>
        ";
            } else {
                // line 53
                yield "            <span style=\"padding:5px 12px;border-radius:15px;font-weight:700;font-size:11px;background:#FFF4E5;color:#FF9800;\">
                ";
                // line 54
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 54)), "html", null, true);
                yield "
            </span>
        ";
            }
            // line 57
            yield "    </div>

        <div style=\"width:100px;text-align:right;font-size:12px;color:#9CA3AF\">
            ";
            // line 60
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "dateEnvoi", [], "any", false, false, false, 60), "d/m/Y"), "html", null, true);
            yield "
        </div>
    </div>
    ";
            $context['_iterated'] = true;
        }
        // line 63
        if (!$context['_iterated']) {
            // line 64
            yield "    <div style=\"text-align:center;padding:50px;color:#95a5a6\">
        Aucun diagnostic envoyé.
    </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['d'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 68
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
        return "agriculteur/diagnostics.html.twig";
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
        return array (  237 => 68,  228 => 64,  226 => 63,  218 => 60,  213 => 57,  207 => 54,  204 => 53,  198 => 50,  195 => 49,  193 => 48,  186 => 44,  178 => 39,  172 => 36,  155 => 21,  150 => 20,  143 => 16,  136 => 11,  123 => 10,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Diagnostics - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('agriculteur_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('agriculteur_irrigation') }}\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"{{ path('agriculteur_diagnostics') }}\" class=\"active\"><span class=\"icon\">📝</span> Diagnostic</a>
{% endblock %}

{% block body %}
<div style=\"display:flex;justify-content:space-between;align-items:center;margin-bottom:30px\">
    <div>
        <div class=\"page-subtitle\">Suivi des analyses</div>
        <div class=\"page-title\">Mes Diagnostics</div>
    </div>
    <a href=\"{{ path('agriculteur_diagnostic_new') }}\" class=\"btn btn-green\">➕ Nouveau Diagnostic</a>
</div>

<div class=\"card\">
    {% for d in diagnostics %}
    <div style=\"display:flex;align-items:center;padding:15px 20px;
                border-bottom:1px solid #F8F8F8;background:white;
                transition:background 0.2s;\"
         onmouseover=\"this.style.background='#FDFDFD'\"
         onmouseout=\"this.style.background='white'\">

        <div style=\"width:80px\">
            <div style=\"width:45px;height:45px;background:#E8F5E9;border-radius:12px;
                        display:flex;align-items:center;justify-content:center;font-size:20px\">
                🌿
            </div>
        </div>

        <div style=\"flex:1;min-width:0\">
            <div style=\"font-weight:700;font-size:14px;color:#374151\">
                {{ d.nomCulture }}
            </div>
            <div style=\"font-size:13px;color:#6B7280;margin-top:2px\">
                {{ d.description ? d.description|slice(0,60) ~ '...' : 'Aucune description' }}
            </div>
        </div>

        <div style=\"width:200px;font-size:13px;color:#6B7280\">
            {{ d.reponseExpert ?? '⏳ En attente de réponse' }}
        </div>

    <div style=\"width:120px;text-align:center\">
        {% if d.statut == 'traite' or d.statut == 'traité' %}
            <span style=\"padding:5px 12px;border-radius:15px;font-weight:700;font-size:11px;background:#E8F5E9;color:#4CAF50;\">
                {{ d.statut|upper }}
            </span>
        {% else %}
            <span style=\"padding:5px 12px;border-radius:15px;font-weight:700;font-size:11px;background:#FFF4E5;color:#FF9800;\">
                {{ d.statut|upper }}
            </span>
        {% endif %}
    </div>

        <div style=\"width:100px;text-align:right;font-size:12px;color:#9CA3AF\">
            {{ d.dateEnvoi|date('d/m/Y') }}
        </div>
    </div>
    {% else %}
    <div style=\"text-align:center;padding:50px;color:#95a5a6\">
        Aucun diagnostic envoyé.
    </div>
    {% endfor %}
</div>
{% endblock %}", "agriculteur/diagnostics.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\agriculteur\\diagnostics.html.twig");
    }
}
