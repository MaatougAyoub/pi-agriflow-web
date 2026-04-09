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

/* plan_irrigation/index.html.twig */
class __TwigTemplate_af401cd275053461bf9f230cd998932c extends Template
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
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "plan_irrigation/index.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "plan_irrigation/index.html.twig"));

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

        yield "Plans d'irrigation";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 3
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

        // line 4
        yield "<h1>Plans d'irrigation</h1>
<a href=\"";
        // line 5
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("plan_new");
        yield "\">+ Nouveau plan</a>
<table border=\"1\" cellpadding=\"8\" style=\"margin-top:20px;width:100%\">
    <tr>
        <th>Culture</th><th>Besoin eau (L)</th><th>Statut</th><th>Date</th><th>Actions</th>
    </tr>
    ";
        // line 10
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable((isset($context["plans"]) || array_key_exists("plans", $context) ? $context["plans"] : (function () { throw new RuntimeError('Variable "plans" does not exist.', 10, $this->source); })()));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
            // line 11
            yield "    <tr>
        <td>";
            // line 12
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, $context["p"], "culture", [], "any", false, false, false, 12), "nom", [], "any", false, false, false, 12), "html", null, true);
            yield "</td>
        <td>";
            // line 13
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "besoinEau", [], "any", false, false, false, 13), "html", null, true);
            yield "</td>
        <td>";
            // line 14
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "statut", [], "any", false, false, false, 14), "html", null, true);
            yield "</td>
        <td>";
            // line 15
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(CoreExtension::getAttribute($this->env, $this->source, $context["p"], "dateCreation", [], "any", false, false, false, 15), "d/m/Y"), "html", null, true);
            yield "</td>
        <td>
            ";
            // line 17
            if ((CoreExtension::getAttribute($this->env, $this->source, $context["p"], "statut", [], "any", false, false, false, 17) == "brouillon")) {
                // line 18
                yield "            <a href=\"";
                yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("plan_approuver", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["p"], "id", [], "any", false, false, false, 18)]), "html", null, true);
                yield "\">Approuver</a>
            ";
            }
            // line 20
            yield "            <a href=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("plan_delete", ["id" => CoreExtension::getAttribute($this->env, $this->source, $context["p"], "id", [], "any", false, false, false, 20)]), "html", null, true);
            yield "\"
               onclick=\"return confirm('Supprimer ?')\">Supprimer</a>
        </td>
    </tr>
    ";
            $context['_iterated'] = true;
        }
        // line 24
        if (!$context['_iterated']) {
            // line 25
            yield "    <tr><td colspan=\"5\">Aucun plan.</td></tr>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['p'], $context['_parent'], $context['_iterated']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 27
        yield "</table>
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
        return "plan_irrigation/index.html.twig";
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
        return array (  163 => 27,  156 => 25,  154 => 24,  144 => 20,  138 => 18,  136 => 17,  131 => 15,  127 => 14,  123 => 13,  119 => 12,  116 => 11,  111 => 10,  103 => 5,  100 => 4,  87 => 3,  64 => 2,  41 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% extends 'base.html.twig' %}
{% block title %}Plans d'irrigation{% endblock %}
{% block body %}
<h1>Plans d'irrigation</h1>
<a href=\"{{ path('plan_new') }}\">+ Nouveau plan</a>
<table border=\"1\" cellpadding=\"8\" style=\"margin-top:20px;width:100%\">
    <tr>
        <th>Culture</th><th>Besoin eau (L)</th><th>Statut</th><th>Date</th><th>Actions</th>
    </tr>
    {% for p in plans %}
    <tr>
        <td>{{ p.culture.nom }}</td>
        <td>{{ p.besoinEau }}</td>
        <td>{{ p.statut }}</td>
        <td>{{ p.dateCreation|date('d/m/Y') }}</td>
        <td>
            {% if p.statut == 'brouillon' %}
            <a href=\"{{ path('plan_approuver', {id: p.id}) }}\">Approuver</a>
            {% endif %}
            <a href=\"{{ path('plan_delete', {id: p.id}) }}\"
               onclick=\"return confirm('Supprimer ?')\">Supprimer</a>
        </td>
    </tr>
    {% else %}
    <tr><td colspan=\"5\">Aucun plan.</td></tr>
    {% endfor %}
</table>
{% endblock %}", "plan_irrigation/index.html.twig", "C:\\Users\\wess\\irrigation3\\irrigation6\\templates\\plan_irrigation\\index.html.twig");
    }
}
