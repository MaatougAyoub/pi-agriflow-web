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

/* expert/diagnostics.html.twig */
class __TwigTemplate_073775e2164c0e0f8335b7353f40e114 extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/diagnostics.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "expert/diagnostics.html.twig"));

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

        yield "Diagnostics Expert - AGRIFLOW";
        
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
        yield "\" class=\"active\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"";
        // line 8
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_produits");
        yield "\"><span class=\"icon\">🧪</span> Produits</a>
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
    <div class=\"page-subtitle\">Gestion des analyses</div>
    <div class=\"page-title\">Diagnostics à traiter</div>
</div>

";
        // line 17
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["diagnostics"]) || array_key_exists("diagnostics", $context) ? $context["diagnostics"] : (function () { throw new RuntimeError('Variable "diagnostics" does not exist.', 17, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["d"]) {
            // line 18
            yield "<div class=\"card\" style=\"margin-bottom:15px\">
    <div style=\"display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:15px\">
        <div style=\"display:flex;align-items:center;gap:12px\">
            <div style=\"width:45px;height:45px;background:#E8F5E9;border-radius:12px;
                        display:flex;align-items:center;justify-content:center;font-size:20px\">
                🌿
            </div>
            <div>
                <div style=\"font-weight:700;font-size:16px;color:#1B4332\">";
            // line 26
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "nomCulture", [], "any", false, false, false, 26), "html", null, true);
            yield "</div>
                <div style=\"font-size:12px;color:#95a5a6\">";
            // line 27
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "dateEnvoi", [], "any", false, false, false, 27), "d/m/Y H:i"), "html", null, true);
            yield "</div>
            </div>
        </div>
        ";
            // line 30
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 30) == "traite")) {
                // line 31
                yield "            <span class=\"badge\" style=\"background:#E8F5E9;color:#4CAF50\">✅ TRAITÉ</span>
        ";
            } else {
                // line 33
                yield "            <span class=\"badge\" style=\"background:#FFF4E5;color:#FF9800\">⏳ EN ATTENTE</span>
        ";
            }
            // line 35
            yield "    </div>

    <div style=\"background:#F9FAFB;padding:12px;border-radius:8px;margin-bottom:15px;color:#6B7280\">
        ";
            // line 38
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "description", [], "any", false, false, false, 38), "html", null, true);
            yield "
    </div>

    ";
            // line 41
            if ((($tmp = CoreExtension::getAttribute($this->env, $this->source, $context["d"], "reponseExpert", [], "any", false, false, false, 41)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 42
                yield "    <div style=\"background:#F0FDF4;padding:12px;border-radius:8px;margin-bottom:15px;
                border-left:3px solid #4CAF50\">
        <div style=\"font-weight:600;color:#1B4332;margin-bottom:6px\">💬 Réponse envoyée :</div>
        <div style=\"color:#374151\">";
                // line 45
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["d"], "reponseExpert", [], "any", false, false, false, 45), "html", null, true);
                yield "</div>
    </div>
    ";
            }
            // line 48
            yield "
    ";
            // line 49
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["d"], "statut", [], "any", false, false, false, 49) != "traite")) {
                // line 50
                yield "    <form method=\"post\" action=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("expert_diagnostic_repondre", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["d"], "id", [], "any", false, false, false, 50)]), "html", null, true);
                yield "\">
        <textarea name=\"reponse\" placeholder=\"Votre réponse d'expert...\"
                  style=\"width:100%;padding:12px;border:1.5px solid #e0e0e0;border-radius:8px;
                         font-size:14px;height:100px;resize:vertical;margin-bottom:10px\"></textarea>
        <div style=\"text-align:right\">
            <button type=\"submit\" class=\"btn btn-green\">💾 Envoyer la réponse</button>
        </div>
    </form>
    ";
            }
            // line 59
            yield "</div>
";
            $context['_iterated'] = true;
        }
        // line 60
        if (!$context['_iterated']) {
            // line 61
            yield "<div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
    Aucun diagnostic.
</div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['d'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "expert/diagnostics.html.twig";
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
        return array (  231 => 61,  229 => 60,  224 => 59,  211 => 50,  209 => 49,  206 => 48,  200 => 45,  195 => 42,  193 => 41,  187 => 38,  182 => 35,  178 => 33,  174 => 31,  172 => 30,  166 => 27,  162 => 26,  152 => 18,  147 => 17,  140 => 12,  127 => 11,  114 => 8,  110 => 7,  106 => 6,  101 => 5,  88 => 4,  65 => 2,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Diagnostics Expert - AGRIFLOW{% endblock %}

{% block sidebar %}
    <a href=\"{{ path('expert_home') }}\"><span class=\"icon\">🏠</span> Dashboard</a>
    <a href=\"{{ path('expert_irrigation') }}\"><span class=\"icon\">💧</span> Irrigation</a>
    <a href=\"{{ path('expert_diagnostics') }}\" class=\"active\"><span class=\"icon\">📝</span> Diagnostics</a>
    <a href=\"{{ path('expert_produits') }}\"><span class=\"icon\">🧪</span> Produits</a>
{% endblock %}

{% block body %}
<div style=\"margin-bottom:30px\">
    <div class=\"page-subtitle\">Gestion des analyses</div>
    <div class=\"page-title\">Diagnostics à traiter</div>
</div>

{% for d in diagnostics %}
<div class=\"card\" style=\"margin-bottom:15px\">
    <div style=\"display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:15px\">
        <div style=\"display:flex;align-items:center;gap:12px\">
            <div style=\"width:45px;height:45px;background:#E8F5E9;border-radius:12px;
                        display:flex;align-items:center;justify-content:center;font-size:20px\">
                🌿
            </div>
            <div>
                <div style=\"font-weight:700;font-size:16px;color:#1B4332\">{{ d.nomCulture }}</div>
                <div style=\"font-size:12px;color:#95a5a6\">{{ d.dateEnvoi|date('d/m/Y H:i') }}</div>
            </div>
        </div>
        {% if d.statut == 'traite' %}
            <span class=\"badge\" style=\"background:#E8F5E9;color:#4CAF50\">✅ TRAITÉ</span>
        {% else %}
            <span class=\"badge\" style=\"background:#FFF4E5;color:#FF9800\">⏳ EN ATTENTE</span>
        {% endif %}
    </div>

    <div style=\"background:#F9FAFB;padding:12px;border-radius:8px;margin-bottom:15px;color:#6B7280\">
        {{ d.description }}
    </div>

    {% if d.reponseExpert %}
    <div style=\"background:#F0FDF4;padding:12px;border-radius:8px;margin-bottom:15px;
                border-left:3px solid #4CAF50\">
        <div style=\"font-weight:600;color:#1B4332;margin-bottom:6px\">💬 Réponse envoyée :</div>
        <div style=\"color:#374151\">{{ d.reponseExpert }}</div>
    </div>
    {% endif %}

    {% if d.statut != 'traite' %}
    <form method=\"post\" action=\"{{ path('expert_diagnostic_repondre', {id: d.id}) }}\">
        <textarea name=\"reponse\" placeholder=\"Votre réponse d'expert...\"
                  style=\"width:100%;padding:12px;border:1.5px solid #e0e0e0;border-radius:8px;
                         font-size:14px;height:100px;resize:vertical;margin-bottom:10px\"></textarea>
        <div style=\"text-align:right\">
            <button type=\"submit\" class=\"btn btn-green\">💾 Envoyer la réponse</button>
        </div>
    </form>
    {% endif %}
</div>
{% else %}
<div class=\"card\" style=\"text-align:center;color:#95a5a6;padding:40px\">
    Aucun diagnostic.
</div>
{% endfor %}
{% endblock %}", "expert/diagnostics.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\expert\\diagnostics.html.twig");
    }
}
