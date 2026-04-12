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

/* agriculteur/diagnostic_detail.html.twig */
class __TwigTemplate_c99a0b09f8d8e320e5e1fdb47fd4b79c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/diagnostic_detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/diagnostic_detail.html.twig"));

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

        yield "Détail Diagnostic - AGRIFLOW";
        
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
        yield "<a href=\"";
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_diagnostics");
        yield "\" class=\"btn btn-gray\" style=\"margin-bottom:25px;display:inline-block\">← Retour</a>

<div class=\"card\">
    <div style=\"display:flex;align-items:center;gap:15px;margin-bottom:25px\">
        <div style=\"width:55px;height:55px;background:#E8F5E9;border-radius:14px;
                    display:flex;align-items:center;justify-content:center;font-size:26px\">
            🌿
        </div>
        <div>
            <div style=\"font-size:20px;font-weight:800;color:#1B4332\">
                ";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 21, $this->source); })()), "nomCulture", [], "any", false, false, false, 21), "html", null, true);
        yield "
            </div>
            <div style=\"color:#95a5a6;font-size:13px;margin-top:3px\">
                ";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 24, $this->source); })()), "dateEnvoi", [], "any", false, false, false, 24), "d/m/Y H:i"), "html", null, true);
        yield "
            </div>
        </div>
    </div>

    <div style=\"margin-bottom:20px\">
        <div style=\"font-weight:700;color:#374151;margin-bottom:8px\">Description</div>
        <div style=\"background:#F9FAFB;padding:15px;border-radius:10px;color:#6B7280;line-height:1.6\">
            ";
        // line 32
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 32, $this->source); })()), "description", [], "any", false, false, false, 32), "html", null, true);
        yield "
        </div>
    </div>

    <div style=\"margin-bottom:20px\">
        <div style=\"font-weight:700;color:#374151;margin-bottom:8px\">Statut</div>
        ";
        // line 38
        if (((CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 38, $this->source); })()), "statut", [], "any", false, false, false, 38) == "traite") || (CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 38, $this->source); })()), "statut", [], "any", false, false, false, 38) == "traité"))) {
            // line 39
            yield "            <span style=\"padding:6px 16px;border-radius:15px;font-weight:700;font-size:12px;background:#E8F5E9;color:#4CAF50;\">
                ";
            // line 40
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 40, $this->source); })()), "statut", [], "any", false, false, false, 40)), "html", null, true);
            yield "
            </span>
        ";
        } else {
            // line 43
            yield "            <span style=\"padding:6px 16px;border-radius:15px;font-weight:700;font-size:12px;background:#FFF4E5;color:#FF9800;\">
                ";
            // line 44
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(Twig\Extension\CoreExtension::upper($this->env->getCharset(), CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 44, $this->source); })()), "statut", [], "any", false, false, false, 44)), "html", null, true);
            yield "
            </span>
        ";
        }
        // line 47
        yield "    </div>
    
    ";
        // line 49
        if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 49, $this->source); })()), "reponseExpert", [], "any", false, false, false, 49)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 50
            yield "    <div style=\"background:#F0FDF4;padding:20px;border-radius:12px;
                border-left:4px solid #4CAF50;margin-top:20px\">
        <div style=\"font-weight:700;color:#1B4332;margin-bottom:10px\">
            💬 Réponse de l'expert
        </div>
        <div style=\"color:#374151;line-height:1.6\">
            ";
            // line 56
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["diagnostic"]) || array_key_exists("diagnostic", $context) ? $context["diagnostic"] : (function () { throw new RuntimeError('Variable "diagnostic" does not exist.', 56, $this->source); })()), "reponseExpert", [], "any", false, false, false, 56), "html", null, true);
            yield "
        </div>
    </div>
    ";
        }
        // line 60
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
        return "agriculteur/diagnostic_detail.html.twig";
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
        return array (  217 => 60,  210 => 56,  202 => 50,  200 => 49,  196 => 47,  190 => 44,  187 => 43,  181 => 40,  178 => 39,  176 => 38,  167 => 32,  156 => 24,  150 => 21,  136 => 11,  123 => 10,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Détail Diagnostic - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('agriculteur_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('agriculteur_irrigation') }}\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"{{ path('agriculteur_diagnostics') }}\" class=\"active\"><span class=\"icon\">📝</span> Diagnostic</a>
{% endblock %}

{% block body %}
<a href=\"{{ path('agriculteur_diagnostics') }}\" class=\"btn btn-gray\" style=\"margin-bottom:25px;display:inline-block\">← Retour</a>

<div class=\"card\">
    <div style=\"display:flex;align-items:center;gap:15px;margin-bottom:25px\">
        <div style=\"width:55px;height:55px;background:#E8F5E9;border-radius:14px;
                    display:flex;align-items:center;justify-content:center;font-size:26px\">
            🌿
        </div>
        <div>
            <div style=\"font-size:20px;font-weight:800;color:#1B4332\">
                {{ diagnostic.nomCulture }}
            </div>
            <div style=\"color:#95a5a6;font-size:13px;margin-top:3px\">
                {{ diagnostic.dateEnvoi|date('d/m/Y H:i') }}
            </div>
        </div>
    </div>

    <div style=\"margin-bottom:20px\">
        <div style=\"font-weight:700;color:#374151;margin-bottom:8px\">Description</div>
        <div style=\"background:#F9FAFB;padding:15px;border-radius:10px;color:#6B7280;line-height:1.6\">
            {{ diagnostic.description }}
        </div>
    </div>

    <div style=\"margin-bottom:20px\">
        <div style=\"font-weight:700;color:#374151;margin-bottom:8px\">Statut</div>
        {% if diagnostic.statut == 'traite' or diagnostic.statut == 'traité' %}
            <span style=\"padding:6px 16px;border-radius:15px;font-weight:700;font-size:12px;background:#E8F5E9;color:#4CAF50;\">
                {{ diagnostic.statut|upper }}
            </span>
        {% else %}
            <span style=\"padding:6px 16px;border-radius:15px;font-weight:700;font-size:12px;background:#FFF4E5;color:#FF9800;\">
                {{ diagnostic.statut|upper }}
            </span>
        {% endif %}
    </div>
    
    {% if diagnostic.reponseExpert %}
    <div style=\"background:#F0FDF4;padding:20px;border-radius:12px;
                border-left:4px solid #4CAF50;margin-top:20px\">
        <div style=\"font-weight:700;color:#1B4332;margin-bottom:10px\">
            💬 Réponse de l'expert
        </div>
        <div style=\"color:#374151;line-height:1.6\">
            {{ diagnostic.reponseExpert }}
        </div>
    </div>
    {% endif %}
</div>
{% endblock %}", "agriculteur/diagnostic_detail.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\agriculteur\\diagnostic_detail.html.twig");
    }
}
