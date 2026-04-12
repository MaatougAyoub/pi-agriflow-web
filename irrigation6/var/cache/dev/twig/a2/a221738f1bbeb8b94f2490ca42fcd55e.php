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

/* agriculteur/irrigation_detail.html.twig */
class __TwigTemplate_c29e9c7c1a71ddb35e357052f56facb6 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/irrigation_detail.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "agriculteur/irrigation_detail.html.twig"));

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

        yield "Détail Plan - ";
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 2, $this->source); })()), "culture", [], "any", false, false, false, 2), "nom", [], "any", false, false, false, 2), "html", null, true);
        
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
        yield "<div style=\"background:linear-gradient(135deg,#1B4332,#2D6A4F);border-radius:14px;
            padding:30px 35px;color:white;display:flex;justify-content:space-between;
            align-items:center;margin-bottom:30px\">
    <div>
        <div style=\"font-size:13px;opacity:0.8;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px\">
            Plan d'irrigation
        </div>
        <div style=\"font-size:24px;font-weight:800;margin-bottom:8px\">";
        // line 18
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 18, $this->source); })()), "culture", [], "any", false, false, false, 18), "nom", [], "any", false, false, false, 18), "html", null, true);
        yield "</div>
        <div style=\"opacity:0.85;font-size:14px\">
            Parcelle: ";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 20, $this->source); })()), "culture", [], "any", false, false, false, 20), "superficie", [], "any", false, false, false, 20), "html", null, true);
        yield " ha &nbsp;|&nbsp;
            Besoin: ";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatNumber(CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 21, $this->source); })()), "besoinEau", [], "any", false, false, false, 21), 1), "html", null, true);
        yield " mm &nbsp;|&nbsp;
            <span style=\"background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:20px\">
                ";
        // line 23
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, (isset($context["plan"]) || array_key_exists("plan", $context) ? $context["plan"] : (function () { throw new RuntimeError('Variable "plan" does not exist.', 23, $this->source); })()), "statut", [], "any", false, false, false, 23), "html", null, true);
        yield "
            </span>
        </div>
    </div>
    <div style=\"text-align:center;opacity:0.9\">
        <div style=\"font-size:42px\">💧</div>
        <div style=\"font-size:11px;letter-spacing:2px;margin-top:4px\">AGRIFLOW</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"margin-bottom:25px;color:#1B4332;font-size:17px\">📅 Planning Hebdomadaire</h3>
    <div style=\"overflow-x:auto\">
    <table style=\"border-collapse:separate;border-spacing:0\">
        ";
        // line 37
        $context["jours"] = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
        // line 38
        yield "        ";
        $context["joursKeys"] = ["LUN", "MAR", "MER", "JEU", "VEN", "SAM", "DIM"];
        // line 39
        yield "        <thead>
            <tr>
                <th style=\"width:130px;background:white;border-bottom:2px solid #f0f0f0\"></th>
                ";
        // line 42
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["jours"]) || array_key_exists("jours", $context) ? $context["jours"] : (function () { throw new RuntimeError('Variable "jours" does not exist.', 42, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["j"]) {
            // line 43
            yield "                <th style=\"text-align:center;padding:12px 8px;background:white;
                           border-bottom:2px solid #f0f0f0;color:#1B4332;font-weight:700\">
                    ";
            // line 45
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($context["j"], "html", null, true);
            yield "
                </th>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['j'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 48
        yield "            </tr>
        </thead>
        <tbody>
            ";
        // line 51
        $context["rows"] = [["EAU (mm)", "eauMm"], ["DURÉE (min)", "dureeMin"], ["TEMP (°C)", "temperature"], ["HUMIDITÉ (%)", "humidite"], ["PLUIE (mm)", "pluieMm"]];
        // line 58
        yield "            ";
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["rows"]) || array_key_exists("rows", $context) ? $context["rows"] : (function () { throw new RuntimeError('Variable "rows" does not exist.', 58, $this->source); })()));
        foreach ($context['_seq'] as $context["_key"] => $context["row"]) {
            // line 59
            yield "            <tr>
                <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;
                           letter-spacing:0.5px;padding:12px 8px;border-bottom:1px solid #f5f5f5\">
                    ";
            // line 62
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["row"], 0, [], "array", false, false, false, 62), "html", null, true);
            yield "
                </td>
                ";
            // line 64
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable((isset($context["joursKeys"]) || array_key_exists("joursKeys", $context) ? $context["joursKeys"] : (function () { throw new RuntimeError('Variable "joursKeys" does not exist.', 64, $this->source); })()));
            foreach ($context['_seq'] as $context["_key"] => $context["k"]) {
                // line 65
                yield "                <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                    <div style=\"width:80px;padding:8px;text-align:center;background:#f8f9fa;
                                border-radius:8px;font-size:14px;color:#374151;margin:auto\">
                        ";
                // line 68
                yield ((CoreExtension::getAttribute($this->env, $this->source, ((CoreExtension::getAttribute($this->env, $this->source, ($context["jourData"] ?? null), $context["k"], [], "array", true, true, false, 68)) ? (CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 68, $this->source); })()), $context["k"], [], "array", false, false, false, 68)) : ([])), CoreExtension::getAttribute($this->env, $this->source, $context["row"], 1, [], "array", false, false, false, 68), [], "any", true, true, false, 68)) ? ((((CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source,                 // line 69
($context["jourData"] ?? null), $context["k"], [], "array", false, true, false, 69), CoreExtension::getAttribute($this->env, $this->source, $context["row"], 1, [], "array", false, false, false, 69), [], "any", true, true, false, 69) &&  !(null === CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 69, $this->source); })()), $context["k"], [], "array", false, false, false, 69), CoreExtension::getAttribute($this->env, $this->source, $context["row"], 1, [], "array", false, false, false, 69), [], "any", false, false, false, 69)))) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["jourData"]) || array_key_exists("jourData", $context) ? $context["jourData"] : (function () { throw new RuntimeError('Variable "jourData" does not exist.', 69, $this->source); })()), $context["k"], [], "array", false, false, false, 69), CoreExtension::getAttribute($this->env, $this->source, $context["row"], 1, [], "array", false, false, false, 69), [], "any", false, false, false, 69), "html", null, true)) : ("—"))) : ("—"));
                yield "
                    </div>
                </td>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['k'], $context['_parent']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 73
            yield "            </tr>
            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['row'], $context['_parent']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 75
        yield "        </tbody>
    </table>
    </div>

    <div style=\"margin-top:25px\">
        <a href=\"";
        // line 80
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("agriculteur_irrigation");
        yield "\" class=\"btn btn-gray\">← Retour</a>
    </div>
</div>
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
        return "agriculteur/irrigation_detail.html.twig";
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
        return array (  260 => 80,  253 => 75,  246 => 73,  236 => 69,  235 => 68,  230 => 65,  226 => 64,  221 => 62,  216 => 59,  211 => 58,  209 => 51,  204 => 48,  195 => 45,  191 => 43,  187 => 42,  182 => 39,  179 => 38,  177 => 37,  160 => 23,  155 => 21,  151 => 20,  146 => 18,  137 => 11,  124 => 10,  111 => 7,  107 => 6,  102 => 5,  89 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Détail Plan - {{ plan.culture.nom }}{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('agriculteur_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('agriculteur_irrigation') }}\" class=\"active\"><span class=\"icon\">💧</span> Plan d'Irrigation</a>
    <a href=\"{{ path('agriculteur_diagnostics') }}\"><span class=\"icon\">📝</span> Diagnostic</a>
{% endblock %}

{% block body %}
<div style=\"background:linear-gradient(135deg,#1B4332,#2D6A4F);border-radius:14px;
            padding:30px 35px;color:white;display:flex;justify-content:space-between;
            align-items:center;margin-bottom:30px\">
    <div>
        <div style=\"font-size:13px;opacity:0.8;margin-bottom:6px;text-transform:uppercase;letter-spacing:1px\">
            Plan d'irrigation
        </div>
        <div style=\"font-size:24px;font-weight:800;margin-bottom:8px\">{{ plan.culture.nom }}</div>
        <div style=\"opacity:0.85;font-size:14px\">
            Parcelle: {{ plan.culture.superficie }} ha &nbsp;|&nbsp;
            Besoin: {{ plan.besoinEau|number_format(1) }} mm &nbsp;|&nbsp;
            <span style=\"background:rgba(255,255,255,0.2);padding:3px 10px;border-radius:20px\">
                {{ plan.statut }}
            </span>
        </div>
    </div>
    <div style=\"text-align:center;opacity:0.9\">
        <div style=\"font-size:42px\">💧</div>
        <div style=\"font-size:11px;letter-spacing:2px;margin-top:4px\">AGRIFLOW</div>
    </div>
</div>

<div class=\"card\">
    <h3 style=\"margin-bottom:25px;color:#1B4332;font-size:17px\">📅 Planning Hebdomadaire</h3>
    <div style=\"overflow-x:auto\">
    <table style=\"border-collapse:separate;border-spacing:0\">
        {% set jours = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] %}
        {% set joursKeys = ['LUN','MAR','MER','JEU','VEN','SAM','DIM'] %}
        <thead>
            <tr>
                <th style=\"width:130px;background:white;border-bottom:2px solid #f0f0f0\"></th>
                {% for j in jours %}
                <th style=\"text-align:center;padding:12px 8px;background:white;
                           border-bottom:2px solid #f0f0f0;color:#1B4332;font-weight:700\">
                    {{ j }}
                </th>
                {% endfor %}
            </tr>
        </thead>
        <tbody>
            {% set rows = [
                ['EAU (mm)', 'eauMm'],
                ['DURÉE (min)', 'dureeMin'],
                ['TEMP (°C)', 'temperature'],
                ['HUMIDITÉ (%)', 'humidite'],
                ['PLUIE (mm)', 'pluieMm']
            ] %}
            {% for row in rows %}
            <tr>
                <td style=\"font-weight:700;font-size:12px;color:#666;text-transform:uppercase;
                           letter-spacing:0.5px;padding:12px 8px;border-bottom:1px solid #f5f5f5\">
                    {{ row[0] }}
                </td>
                {% for k in joursKeys %}
                <td style=\"padding:8px;border-bottom:1px solid #f5f5f5;text-align:center\">
                    <div style=\"width:80px;padding:8px;text-align:center;background:#f8f9fa;
                                border-radius:8px;font-size:14px;color:#374151;margin:auto\">
                        {{ attribute(jourData[k] is defined ? jourData[k] : {}, row[1]) is defined
                           ? attribute(jourData[k], row[1]) ?? '—' : '—' }}
                    </div>
                </td>
                {% endfor %}
            </tr>
            {% endfor %}
        </tbody>
    </table>
    </div>

    <div style=\"margin-top:25px\">
        <a href=\"{{ path('agriculteur_irrigation') }}\" class=\"btn btn-gray\">← Retour</a>
    </div>
</div>
{% endblock %}", "agriculteur/irrigation_detail.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\agriculteur\\irrigation_detail.html.twig");
    }
}
